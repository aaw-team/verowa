<?php
declare(strict_types=1);
namespace AawTeam\Verowa\Http\Middleware;
/*
 * Copyright by Agentur am Wasser | Maeder & Partner AG
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Spatie\IcalendarGenerator\Components\Calendar;
use Spatie\IcalendarGenerator\Components\Event;
use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * EventController
 */
class IcsEventDownload implements MiddlewareInterface
{
    /**
     * @var ResponseFactoryInterface
     */
    protected $responseFactory;

    /**
     * @var StreamFactoryInterface
     */
    protected $streamFactory;

    /**
     * @param ResponseFactoryInterface $responseFactory
     * @param StreamFactoryInterface $streamFactory
     */
    public function __construct(
        ResponseFactoryInterface $responseFactory,
        StreamFactoryInterface $streamFactory
    ) {
        $this->responseFactory = $responseFactory;
        $this->streamFactory = $streamFactory;
    }

    /**
     * {@inheritDoc}
     * @see \Psr\Http\Server\MiddlewareInterface::process()
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if (!is_array($request->getQueryParams()['tx_verowa'])
            || !isset($request->getQueryParams()['tx_verowa']['action'])
            || $request->getQueryParams()['tx_verowa']['action'] !== 'icsdownload'
        ){
            return $handler->handle($request);
        }

        $eventUid = (int)($request->getQueryParams()['tx_verowa']['event'] ?? 0);
        if ($eventUid < 1) {
            // Bad request
            return $this->responseFactory->createResponse(400);
        }

        // Load data
        $qb = $this->getDatabaseConnection()->createQueryBuilder();
        $qb->select(
            'event.*',
            'room.room_name',
            'room.location_name'
        )
        ->from('tx_verowa_event', 'event')
        ->leftJoin(
            'event',
            'tx_verowa_event_room_mm',
            'event_room_mm',
            $qb->expr()->eq('event.uid', $qb->quoteIdentifier('event_room_mm.uid_local'))
        )
        ->leftJoin(
            'event_room_mm',
            'tx_verowa_room',
            'room',
            $qb->expr()->eq('event_room_mm.uid_foreign', $qb->quoteIdentifier('room.uid'))
        )
        ->where(
            $qb->expr()->andX(
                $qb->expr()->eq('event.uid', $qb->createNamedParameter($eventUid, \PDO::PARAM_INT))
            )
        );

        if (!($event = $qb->execute()->fetch())) {
            // Record not found
            return $this->responseFactory->createResponse(404);
        }

        // Run the iCalendar generation
        if (!class_exists(Calendar::class)){
            $pharFile = 'icalendar-generator-2.phar';
            if (PHP_VERSION_ID < 70400) {
                $pharFile = 'icalendar-generator-1.phar';
            }

            /** @var \Composer\Autoload\ClassLoader $autoloader */
            $loader = require 'phar://' . GeneralUtility::getFileAbsFileName('EXT:verowa/Resources/Private/PHP/IcalendarGenerator/' . $pharFile . '/vendor/autoload.php');
            $loader->register();
        }

        // Create event
        $eventIcalendarUid = 'verowa-event-' . substr(hash('md5', implode('-', [$event['uid'], $event['event_id'], $event['date_from'], $event['date_to']])), 0, 16);
        $icalendarEvent = Event::create()
            ->name($event['title'])
            ->uniqueIdentifier($eventIcalendarUid)
            ->createdAt(new \DateTimeImmutable())
            ->startsAt($this->timestamp2DateTime($event['date_from']))
            ->endsAt($this->timestamp2DateTime($event['date_to']))
        ;

        // This method only exists in version 1.x, as of v2 timezones are handeled automatically
        if (method_exists($icalendarEvent, 'withTimezone')){
            $icalendarEvent->withTimezone();
        }

        // Add description
        if (($event['short_desc'] ?? '') !== '') {
            $icalendarEvent->description(strip_tags($event['short_desc']));
        } elseif (($event['long_desc'] ?? '') !== '') {
            $icalendarEvent->description(strip_tags($event['long_desc']));
        }

        // Add location
        if (($event['room_name'] ?? '') !== '') {
            $icalendarEvent->address($event['room_name']);
        } elseif (($event['location_name'] ?? '') !== '') {
            $icalendarEvent->address($event['location_name']);
        }

        // Create calendar
        $calendar = Calendar::create()
            ->productIdentifier('TYPO3 CMS Extension aaw-team/verowa')
            ->event($icalendarEvent);

        // Return the response
        $stream = $this->streamFactory->createStream($calendar->get());
        return $this->responseFactory->createResponse()
            ->withHeader('Content-Type', 'text/calendar; charset=utf-8')
            ->withHeader('Content-Disposition', 'attachment; filename=' . $eventIcalendarUid . '.ics')
            ->withHeader('Content-Length', (string)$stream->getSize())
            ->withBody($stream);
    }

    /**
     * @param string $dateString
     * @return \DateTimeImmutable
     */
    protected function timestamp2DateTime(int $timestamp): \DateTimeInterface
    {
        $format = 'Y-m-d\TH:i:s';
        return \DateTimeImmutable::createFromFormat($format, date($format, $timestamp), new \DateTimeZone(date_default_timezone_get()));
    }

    /**
     * @return Connection
     */
    protected function getDatabaseConnection(): Connection
    {
        return GeneralUtility::makeInstance(ConnectionPool::class)->getConnectionByName(ConnectionPool::DEFAULT_CONNECTION_NAME);
    }
}
