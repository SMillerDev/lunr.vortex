<?php

/**
 * This file contains an abstraction for the response from the MPNS server.
 *
 * @package    Lunr\Vortex\MPNS
 * @author     Heinz Wiesinger <heinz@m2mobi.com>
 * @copyright  2013-2018, M2Mobi BV, Amsterdam, The Netherlands
 * @license    http://lunr.nl/LICENSE MIT License
 */

namespace Lunr\Vortex\MPNS;

use Lunr\Vortex\PushNotificationStatus;
use Lunr\Vortex\PushNotificationResponseInterface;
use Psr\Log\LoggerInterface;
use Requests_Response;
use Requests_Response_Headers;

/**
 * Windows Phone Push Notification response wrapper.
 */
class MPNSResponse implements PushNotificationResponseInterface
{

    /**
     * HTTP headers of the response.
     * @var Requests_Response_Headers
     */
    private Requests_Response_Headers $headers;

    /**
     * HTTP status code.
     * @var bool
     */
    private $http_code;

    /**
     * Delivery status.
     * @var PushNotificationStatus::*
     */
    private int $status;

    /**
     * Push notification endpoint.
     * @var string
     */
    private string $endpoint;

    /**
     * Raw payload that was sent to MPNS.
     * @var string
     */
    protected string $payload;

    /**
     * Constructor.
     *
     * @param Requests_Response $response Requests_Response object.
     * @param LoggerInterface   $logger   Shared instance of a Logger.
     * @param string            $payload  Raw payload that was sent to MPNS.
     */
    public function __construct(Requests_Response $response, LoggerInterface $logger, string $payload)
    {
        $this->http_code = $response->status_code;
        $this->endpoint  = $response->url;
        $this->payload   = $payload;

        if ($this->http_code === FALSE)
        {
            $this->headers = new Requests_Response_Headers();
            $this->status  = PushNotificationStatus::ERROR;
        }
        else
        {
            $this->set_headers($response->headers);
            $this->set_status($response->url, $logger);
        }
    }

    /**
     * Destructor.
     */
    public function __destruct()
    {
        unset($this->headers);
        unset($this->http_code);
        unset($this->status);
        unset($this->payload);
    }

    /**
     * Set response header information.
     *
     * @param Requests_Response_Headers $headers Response headers
     *
     * @return void
     */
    private function set_headers(Requests_Response_Headers $headers): void
    {
        $this->headers = $headers;

        if (in_array($this->http_code, [ 400, 401, 405, 503 ]))
        {
            unset($this->headers['X-Notificationstatus']);
            unset($this->headers['X-Deviceconnectionstatus']);
            unset($this->headers['X-Subscriptionstatus']);
            $this->headers['X-Notificationstatus']     = 'N/A';
            $this->headers['X-Deviceconnectionstatus'] = 'N/A';
            $this->headers['X-Subscriptionstatus']     = 'N/A';
        }
        elseif ($this->http_code === 412)
        {
            unset($this->headers['X-Subscriptionstatus']);
            $this->headers['X-Subscriptionstatus'] = 'N/A';
        }
    }

    /**
     * Set notification status information.
     *
     * @param string          $endpoint The notification endpoint that was used.
     * @param LoggerInterface $logger   Shared instance of a Logger.
     *
     * @return void
     */
    private function set_status(string $endpoint, LoggerInterface $logger)
    {
        switch ($this->http_code)
        {
            case 200:
                if ($this->headers['X-Notificationstatus'] === 'Received')
                {
                    $this->status = PushNotificationStatus::SUCCESS;
                }
                elseif ($this->headers['X-Notificationstatus'] === 'QueueFull')
                {
                    $this->status = PushNotificationStatus::TEMPORARY_ERROR;
                }
                else
                {
                    $this->status = PushNotificationStatus::CLIENT_ERROR;
                }

                break;
            case 404:
                $this->status = PushNotificationStatus::INVALID_ENDPOINT;
                break;
            case 400:
            case 401:
            case 405:
                $this->status = PushNotificationStatus::ERROR;
                break;
            case 406:
            case 412:
            case 503:
                $this->status = PushNotificationStatus::TEMPORARY_ERROR;
                break;
            default:
                $this->status = PushNotificationStatus::UNKNOWN;
                break;
        }

        if ($this->status !== PushNotificationStatus::SUCCESS)
        {
            $context = [
                'endpoint' => $endpoint,
                'nstatus'  => $this->headers['X-Notificationstatus'],
                'dstatus'  => $this->headers['X-Deviceconnectionstatus'],
                'sstatus'  => $this->headers['X-Subscriptionstatus'],
            ];

            $message  = 'MPNS notification delivery status for endpoint {endpoint}: ';
            $message .= '{nstatus}, device {dstatus}, subscription {sstatus}';

            $logger->warning($message, $context);
        }
    }

    /**
     * Get notification delivery status for an endpoint.
     *
     * @param string $endpoint Endpoint
     *
     * @return PushNotificationStatus::* Delivery status for the endpoint
     */
    public function get_status(string $endpoint): int
    {
        if ($endpoint != $this->endpoint)
        {
            return PushNotificationStatus::UNKNOWN;
        }

        return $this->status;
    }

}

?>
