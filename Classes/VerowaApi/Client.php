<?php
declare(strict_types=1);
namespace AawTeam\Verowa\VerowaApi;
/*
 * Copyright by Agentur am Wasser | Maeder & Partner AG
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

use GuzzleHttp\RequestOptions;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use TYPO3\CMS\Core\Http\RequestFactory;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Client
 */
class Client implements LoggerAwareInterface
{
    use LoggerAwareTrait;

    public const API_URI = 'https://api.verowa.ch/';

    /**
     * @var string
     */
    private $instance;

    /**
     * @var string
     */
    private $apikey;

    /**
     * @param string $instance
     * @param string $apikey
     * @param RequestFactoryInterface $requestFactory
     */
    public function __construct(string $instance, string $apikey)
    {
        $this->instance = $instance;
        $this->apikey = $apikey;
    }

    /**
     * @return array|null
     */
    public function getRooms(): ?array
    {
        $data = $this->request('getrooms');
        if (is_array($data)) {
            return $data;
        }
        return null;
    }

    /**
     * @return array|null
     */
    public function getEvents(): ?array
    {
        $data = $this->request('getevents');
        if (is_array($data) && is_array($data['events'])) {
            return $data['events'];
        }
        return null;
    }

    /**
     * @param int $eventId
     * @return array|null
     */
    public function getEventDetails(int $eventId): ?array
    {
        $data = $this->request('geteventdetails', [$eventId]);
        if (is_array($data) && is_array($data[0])) {
            return $data[0];
        }
        return null;
    }

    /**
     * @param string $function
     * @param array $parameters
     * @throws \Exception
     * @return array|null
     */
    public function request(string $function, array $parameters = []): ?array
    {
        $requestURI = $this->buildApiFunctionRequestUri($function);
        foreach ($parameters as $parameterValue) {
            $requestURI .= '/' . urlencode($parameterValue);
        }

        try {
            $response = $this->sendApiRequest($requestURI);
        } catch (\Exception $e) {
            $this->logger->error('Cannot request the Verowa API', [
                'exception' => [
                    'type' => get_class($e),
                    'message' => $e->getMessage(),
                    'code' => $e->getCode(),
                ],
            ]);
            throw new \Exception('Cannot request the Verowa API', 0, $e);
        }

        $responseBody = $response->getBody()->getContents();
        $decodedResponse = null;

        if (in_array($response->getHeaderLine('content-type'), ['application/json', 'text/json'], true)) {
            if (empty($responseBody)) {
                return null;
            }
            $decodedResponse = \json_decode($responseBody, true);
            if (!is_array($decodedResponse)) {
                $this->logger->error('Cannot decode JSON from response body');
                throw new \Exception('Cannot decode JSON from response body');
            }
        } else {
            $this->logger->error('Got unknown content-type header', [
                $response->getHeaderLine('content-type')
            ]);
            throw new \Exception('Got unknown content-type header: ' . htmlspecialchars($response->getHeaderLine('content-type')), 0);
        }

        return $decodedResponse;
    }

    /**
     * @param string $function
     * @return string
     */
    protected function buildApiFunctionRequestUri(string $function): string
    {
        return sprintf(
            '%s%s/%s/%s',
            self::API_URI,
            $function,
            $this->instance,
            $this->apikey
        );
    }

    /**
     * @param string $uri
     * @return ResponseInterface
     */
    protected function sendApiRequest(string $uri): ResponseInterface
    {
        return GeneralUtility::makeInstance(RequestFactory::class)->request(
            $uri,
            'GET',
            [
                RequestOptions::ALLOW_REDIRECTS => false,
                RequestOptions::COOKIES => false,
                RequestOptions::TIMEOUT => 10.0,
            ]
        );
    }
}
