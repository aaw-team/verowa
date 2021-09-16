<?php
declare(strict_types=1);
namespace AawTeam\Verowa\Command;
/*
 * Copyright by Agentur am Wasser | Maeder & Partner AG
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

use AawTeam\Verowa\VerowaApi\Client;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use TYPO3\CMS\Core\Core\Bootstrap;
use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\DataHandling\DataHandler;
use TYPO3\CMS\Core\Locking\LockFactory;
use TYPO3\CMS\Core\Locking\LockingStrategyInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * ImportCommand
 */
class ImportCommand extends Command
{
    protected static $defaultName = 'Verowa Data Importer';

    /**
     * {@inheritDoc}
     * @see \Symfony\Component\Console\Command\Command::configure()
     */
    protected function configure()
    {
        $this->setDescription('Import data from Verowa REST API');

        $this->addOption(
            'instance',
            null,
            InputOption::VALUE_REQUIRED,
            'Paramter ":instance" for the Verowa API'
            );
        $this->addOption(
            'apikey',
            null,
            InputOption::VALUE_REQUIRED,
            'Paramter ":apikey" for the Verowa API'
            );
        $this->addOption(
            'storage-pid',
            null,
            InputOption::VALUE_REQUIRED,
            'The Storage PID for the imported data'
        );
        $this->addOption(
            'only-events',
            null,
            InputOption::VALUE_NONE,
            'Only import event data, no relations. Note: this switch is not functional at the moment, because there is no support for other record types yet.'
        );
    }

    /**
     * {@inheritDoc}
     * @see \Symfony\Component\Console\Command\Command::execute()
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // Make sure the _cli_ user is loaded
        Bootstrap::initializeBackendAuthentication();

        // Input validation
        $instance = $input->getOption('instance');
        if (!is_string($instance) || empty($instance)) {
            $output->writeln('Error: you MUST specify --instance', OutputInterface::VERBOSITY_QUIET);
            return Command::FAILURE;
        }

        $apikey = $input->getOption('apikey');
        if (!is_string($apikey) || empty($apikey)) {
            $output->writeln('Error: you MUST specify --apikey', OutputInterface::VERBOSITY_QUIET);
            return Command::FAILURE;
        }

        $storagePid = (int)$input->getOption('storage-pid');
        if ($storagePid < 1) {
            $output->writeln('Error: you MUST specify --storage-pid', OutputInterface::VERBOSITY_QUIET);
            return Command::FAILURE;
        }

        // Acquire lock
        try {
            $locker = $this->getLocker('verowa-import');
            $locker->acquire();
        } catch (\TYPO3\CMS\Core\Locking\Exception\LockCreateException $e) {
            $output->writeln('Error: cannot create lock: ' . $e->getMessage(), OutputInterface::VERBOSITY_QUIET);
            return Command::FAILURE;
        } catch (\Exception $e) {
            $output->writeln('Error: cannot acquire lock: ' . $e->getMessage(), OutputInterface::VERBOSITY_QUIET);
            return Command::FAILURE;
        }

        try {
            $this->getDatabaseConnection()->beginTransaction();

            /** @var Client $apiClient */
            $apiClient = GeneralUtility::makeInstance(Client::class, $instance, $apikey);

            // Import room data
            if ($input->getOption('only-events') !== true) {
                $allRoomIds = [];
                $roomInserts = $roomUpdates = 0;
                $roomId2RoomUid = [];
                foreach ($apiClient->getRooms() as $room) {
                    $roomId = (int)$room['room_id'];
                    $allRoomIds[] = $roomId;
                    $roomData = [
                        'pid' => $storagePid,
                        'tstamp' => $GLOBALS['EXEC_TIME'],
                        'room_id' => $roomId,
                        'room_name' => $room['room_name'],
                        'shortcut' => $room['shortcut'],
                        'location_id' => (int)$room['location_id'],
                        'location_name' => $room['location_name'],
                        'street' => $room['street'],
                        'postcode' => $room['postcode'],
                        'city' => $room['city'],
                        'location_url' => $room['location_url'],
                        'location_url_is_external' => (bool)$room['location_url_is_external'],
                    ];
                    $roomDataTypes = [
                        \PDO::PARAM_INT,
                        \PDO::PARAM_INT,
                        \PDO::PARAM_INT,
                        \PDO::PARAM_STR,
                        \PDO::PARAM_STR,
                        \PDO::PARAM_INT,
                        \PDO::PARAM_STR,
                        \PDO::PARAM_STR,
                        \PDO::PARAM_STR,
                        \PDO::PARAM_STR,
                        \PDO::PARAM_STR,
                        \PDO::PARAM_BOOL,
                    ];
                    $qb = $this->getDatabaseConnection()->createQueryBuilder();
                    $qb->getRestrictions()->removeAll();
                    $qb->selectLiteral('*')->from('tx_verowa_room')->where(
                        $qb->expr()->andX(
                            $qb->expr()->eq('pid', $qb->createNamedParameter($storagePid, \PDO::PARAM_INT)),
                            $qb->expr()->eq('room_id', $qb->createNamedParameter($room['room_id'], \PDO::PARAM_INT))
                        )
                    );
                    if ($dbRoom = $qb->execute()->fetch()) {
                        $roomUpdates += $this->getDatabaseConnection()->update(
                            'tx_verowa_room',
                            $roomData,
                            ['uid' => $dbRoom['uid']],
                            $roomDataTypes
                        );
                        $roomId2RoomUid[$roomId] = $dbRoom['uid'];
                    } else {
                        $roomData['crdate'] = $GLOBALS['EXEC_TIME'];
                        $roomDataTypes[] = \PDO::PARAM_INT;
                        $roomInserts += $this->getDatabaseConnection()->insert(
                            'tx_verowa_room',
                            $roomData,
                            $roomDataTypes
                        );
                        $roomId2RoomUid[$roomId] = $this->getDatabaseConnection()->lastInsertId('tx_verowa_room');
                    }
                }

                // Remove superfluous room records
                $qb = $this->getDatabaseConnection()->createQueryBuilder();
                $qb->getRestrictions()->removeAll();
                $qb->delete('tx_verowa_room')->from('tx_verowa_room')->where(
                    $qb->expr()->eq('pid', $storagePid)
                );
                if (!empty($allRoomIds)) {
                    $qb->andWhere(
                        $qb->expr()->notIn('room_id', $qb->createNamedParameter($allRoomIds, Connection::PARAM_INT_ARRAY))
                    );
                }
                $roomDeletions = $qb->execute();

                $output->writeln('Room inserts: ' . $roomInserts . ' updates: ' . $roomUpdates . ' deletes: ' . $roomDeletions, OutputInterface::VERBOSITY_VERBOSE);
            }

            // Remove all file records, they will be created during the event import
            $qb = $this->getDatabaseConnection()->createQueryBuilder();
            $qb->getRestrictions()->removeAll();
            $fileDeletions = $qb->delete('tx_verowa_file')->from('tx_verowa_file')->where(
                $qb->expr()->eq('pid', $qb->createNamedParameter($storagePid, \PDO::PARAM_INT))
            )->execute();
            $output->writeln('File deletes: ' . $fileDeletions, OutputInterface::VERBOSITY_VERBOSE);

            // Import events
            $allEventIds = [];
            $eventInserts = $eventUpdates = $eventDeletions = 0;
            $fileInserts = 0;
            $eventId2EventUid = [];
            foreach ($apiClient->getEvents() as $event) {
                $eventId = (int)$event['event_id'];
                $allEventIds[] = $eventId;

                $subscribeDate = $this->verowaDateString2DateTimeImmutable($event['subscribe_date']);
                $subscribeDateType = \PDO::PARAM_INT;
                if ($subscribeDate !== null) {
                    $subscribeDate = $subscribeDate->getTimestamp();
                } else {
                    $subscribeDateType = \PDO::PARAM_BOOL;
                }

                $eventDetails = $apiClient->getEventDetails($eventId);

                $eventData = [
                    'pid' => $storagePid,
                    'tstamp' => $GLOBALS['EXEC_TIME'],
                    'event_id' => $eventId,
                    // JJJJ-MM-DD HH:MM:SS
                    'date_from' => $this->verowaDateTimeString2DateTimeImmutable($event['date_from'])->getTimestamp(),
                    'date_to' => $this->verowaDateTimeString2DateTimeImmutable($event['date_to'])->getTimestamp(),
                    'hide_time' => (bool)$event['hide_time'],
                    'date_text' => $event['date_text'],
                    'title' => trim((string)$event['title']),
                    'topic' => $event['topic'],
                    'short_desc' => $event['short_desc'],
                    'long_desc' => $event['long_desc'],
                    // 'organizer' => (int)$event['organizer']['id'] ?? 0,     // @todo
                    // 'coorganizers' => 0,                                    // @todo
                    // 'further_coorganizers' => '',                           // @todo
                    // 'lectors' => 0,                                         // @todo
                    // 'visitators' => 0,                                      // @todo
                    // 'organists' => 0,                                       // @todo
                    // 'vergers' => 0,                                         // @todo
                    // 'catering' => '',                                       // @todo
                    // 'with_sacrament' => '',                                 // @todo
                    // 'childcare_id' => 0,                                    // @todo
                    // 'childcare_text' => '',                                 // @todo
                    // 'childcare_person' => 0,                                // @todo
                    'subscribe_date' => $subscribeDate,
                    // 'subscribe_person' => 0,                                // @todo
                    // 'baptism_offer_id' => 0,                                // @todo
                    // 'baptism_offer_text' => '',                             // @todo
                    // 'collection' => 0,                                      // @todo
                    // 'target_groups' => 0,                                   // @todo
                    // 'layers' => 0,                                          // @todo
                    // 'rooms' => 0,                                           // @todo
                    // 'files' => 0,                                           // @todo
                    'image_url' => (string)$event['image_url'],                // @todo
                    'image_width' => (int)$eventDetails['image_width'] ?? 0,
                    'image_height' => (int)$eventDetails['image_height'] ?? 0,
                ];
                $eventDataTypes = [
                    \PDO::PARAM_INT,
                    \PDO::PARAM_INT,
                    \PDO::PARAM_INT,
                    \PDO::PARAM_INT,
                    \PDO::PARAM_INT,
                    \PDO::PARAM_BOOL,
                    \PDO::PARAM_STR,
                    \PDO::PARAM_STR,
                    \PDO::PARAM_STR,
                    \PDO::PARAM_STR,
                    \PDO::PARAM_STR,
                    // \PDO::PARAM_INT,
                    // \PDO::PARAM_INT,
                    // \PDO::PARAM_STR,
                    // \PDO::PARAM_INT,
                    // \PDO::PARAM_INT,
                    // \PDO::PARAM_INT,
                    // \PDO::PARAM_INT,
                    // \PDO::PARAM_STR,
                    // \PDO::PARAM_BOOL,
                    // \PDO::PARAM_INT,
                    // \PDO::PARAM_STR,
                    // \PDO::PARAM_INT,
                    $subscribeDateType,
                    // \PDO::PARAM_INT,
                    // \PDO::PARAM_INT,
                    // \PDO::PARAM_STR,
                    // \PDO::PARAM_INT,
                    // \PDO::PARAM_INT,
                    // \PDO::PARAM_INT,
                    // \PDO::PARAM_INT,
                    // \PDO::PARAM_INT,
                    \PDO::PARAM_STR,
                    \PDO::PARAM_INT,
                    \PDO::PARAM_INT,
                ];

                $qb = $this->getDatabaseConnection()->createQueryBuilder();
                $qb->getRestrictions()->removeAll();
                $qb->selectLiteral('*')->from('tx_verowa_event')->where(
                    $qb->expr()->andX(
                        $qb->expr()->eq('pid', $qb->createNamedParameter($storagePid, \PDO::PARAM_INT)),
                        $qb->expr()->eq('event_id', $qb->createNamedParameter($eventId, \PDO::PARAM_INT))
                    )
                );
                if ($dbEvent = $qb->execute()->fetch()) {
                    $eventUpdates += $this->getDatabaseConnection()->update(
                        'tx_verowa_event',
                        $eventData,
                        ['uid' => $dbEvent['uid']],
                        $eventDataTypes
                    );
                    $eventId2EventUid[$eventId] = $dbEvent['uid'];
                } else {
                    $eventData['crdate'] = $GLOBALS['EXEC_TIME'];
                    $eventDataTypes[] = \PDO::PARAM_INT;
                    $eventInserts += $this->getDatabaseConnection()->insert(
                        'tx_verowa_event',
                        $eventData,
                        $eventDataTypes
                    );
                    $eventId2EventUid[$eventId] = $this->getDatabaseConnection()->lastInsertId('tx_verowa_event');
                }

                // Add file records
                foreach ($eventDetails['files'] as $file) {
                    $fileInserts += $this->getDatabaseConnection()->insert(
                        'tx_verowa_file',
                        [
                            'pid' => $storagePid,
                            'tstamp' => $GLOBALS['EXEC_TIME'],
                            'event' => $eventId2EventUid[$eventId],
                            'file_name' => $file['file_name'],
                            'desc' => $file['desc'],
                            'url' => $file['url'],
                            'filesize_kb' => $file['filesize_kb'],
                            'file_type' => $file['file_type'],
                        ],
                        [
                            \PDO::PARAM_INT,
                            \PDO::PARAM_INT,
                            \PDO::PARAM_INT,
                            \PDO::PARAM_STR,
                            \PDO::PARAM_STR,
                            \PDO::PARAM_STR,
                            \PDO::PARAM_INT,
                            \PDO::PARAM_STR,
                        ]
                    );
                }

                // Add relations to room records
                $qb = $this->getDatabaseConnection()->createQueryBuilder();
                $qb->getRestrictions()->removeAll();
                $qb->delete('tx_verowa_event_room_mm')->from('tx_verowa_event_room_mm')->where(
                    $qb->expr()->eq('uid_local', $qb->createNamedParameter($eventId2EventUid[$eventId], \PDO::PARAM_INT))
                )->execute();
                if (!empty($eventDetails['rooms'])) {
                    foreach ($eventDetails['rooms'] as $room) {
                        $sorting = 0;
                        if (!isset($room['id']) || intval($room['id']) < 1) {
                            // Create a 'temporary' room record for events, that do not specify a room.id (and seem therefor not to be present in /getrooms API call)
                            $this->getDatabaseConnection()->insert(
                                'tx_verowa_room',
                                [
                                    'pid' => $storagePid,
                                    'tstamp' => $GLOBALS['EXEC_TIME'],
                                    'room_name' => (string)$room['name'],
                                    'location_name' => (string)$room['location_name'],
                                    'location_url' => (string)$room['location_url'],
                                    'location_url_is_external' => (bool)$room['location_url_is_external'],
                                ],
                                [
                                    \PDO::PARAM_INT,
                                    \PDO::PARAM_INT,
                                    \PDO::PARAM_STR,
                                    \PDO::PARAM_STR,
                                    \PDO::PARAM_STR,
                                    \PDO::PARAM_BOOL,
                                ]
                            );

                            // Make up a room.id that doesn't exist, so it can be used afterwards to link event and room record
                            $tmpRoomId = max($roomId2RoomUid) + 1;
                            while (in_array($tmpRoomId, $roomId2RoomUid)) {
                                $tmpRoomId++;
                            }
                            $room['id'] = $tmpRoomId;
                            $roomId2RoomUid[$room['id']] = $this->getDatabaseConnection()->lastInsertId('tx_verowa_room', 'uid');
                        }
                        $this->getDatabaseConnection()->insert(
                            'tx_verowa_event_room_mm',
                            [
                                'uid_local' => $eventId2EventUid[$eventId],
                                'uid_foreign' => $roomId2RoomUid[$room['id']],
                                'sorting' => (++$sorting),
                            ],
                            [\PDO::PARAM_INT, \PDO::PARAM_INT, \PDO::PARAM_INT]
                        );
                    }
                    $this->getDatabaseConnection()->update(
                        'tx_verowa_event',
                        ['rooms' => count($eventDetails['rooms'])],
                        ['uid' => $eventId2EventUid[$eventId]],
                        [\PDO::PARAM_INT]
                    );
                }
            }

            // Remove superfluous event records
            $qb = $this->getDatabaseConnection()->createQueryBuilder();
            $qb->getRestrictions()->removeAll();
            $qb->delete('tx_verowa_event')->from('tx_verowa_event')->where(
                $qb->expr()->eq('pid', $storagePid)
            );
            if (!empty($allEventIds)) {
                $qb->andWhere(
                    $qb->expr()->notIn('event_id', $qb->createNamedParameter($allEventIds, Connection::PARAM_INT_ARRAY))
                );
            }
            $eventDeletions = $qb->execute();

            $output->writeln('Event inserts: ' . $eventInserts . ' updates: ' . $eventUpdates . ' deletes: ' . $eventDeletions, OutputInterface::VERBOSITY_VERBOSE);
            $output->writeln('File inserts: ' . $fileInserts, OutputInterface::VERBOSITY_VERBOSE);

            // Commit transaction
            $this->getDatabaseConnection()->commit();
        } catch (\Exception $e) {
            $this->getDatabaseConnection()->rollBack();
            $output->writeln('An error occured while running import: ' . $e->getMessage(), OutputInterface::VERBOSITY_QUIET);
            $locker->release();
            throw $e;
        }

        // Clear page cache of $storagePid
        /** @var DataHandler $dataHandler */
        $dataHandler = GeneralUtility::makeInstance(DataHandler::class);
        $dataHandler->start([], []);
        $dataHandler->clear_cacheCmd((string)$storagePid);

        // Release the lock
        $locker->release();
        return Command::SUCCESS;
    }

    /**
     * @param string $dateString
     * @return \DateTimeImmutable
     */
    protected function verowaDateString2DateTimeImmutable(string $dateString): ?\DateTimeImmutable
    {
        $dateTime = \DateTimeImmutable::createFromFormat('Y-m-d', $dateString, new \DateTimeZone(date_default_timezone_get()));
        if ($dateTime === false) {
            return null;
        }
        return $dateTime;
    }

    /**
     * @param string $dateString
     * @return \DateTimeImmutable
     */
    protected function verowaDateTimeString2DateTimeImmutable(string $dateTimeString): \DateTimeImmutable
    {
        return \DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $dateTimeString, new \DateTimeZone(date_default_timezone_get()));
    }

    /**
     * @return \TYPO3\CMS\Core\Locking\LockingStrategyInterface
     */
    protected function getLocker(string $key) : LockingStrategyInterface
    {
        return GeneralUtility::makeInstance(LockFactory::class)->createLocker($key);
    }

    /**
     * @return Connection
     */
    protected function getDatabaseConnection(): Connection
    {
        return GeneralUtility::makeInstance(ConnectionPool::class)->getConnectionByName(ConnectionPool::DEFAULT_CONNECTION_NAME);
    }
}
