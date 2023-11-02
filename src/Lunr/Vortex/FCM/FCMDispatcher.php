<?php

/**
 * This file contains functionality to dispatch Firebase Cloud Messaging Push Notifications.
 *
 * @package    Lunr\Vortex\FCM
 * @author     Patrick Valk <p.valk@m2mobi.com>
 * @copyright  2017-2018, M2Mobi BV, Amsterdam, The Netherlands
 * @license    http://lunr.nl/LICENSE MIT License
 */

namespace Lunr\Vortex\FCM;

use Lunr\Vortex\PushNotificationMultiDispatcherInterface;
use Lunr\Vortex\PushNotificationResponseInterface;
use Psr\Log\LoggerInterface;
use Requests_Exception;
use Requests_Response;
use Requests_Session;

/**
 * Firebase Cloud Messaging Push Notification Dispatcher.
 */
class FCMDispatcher implements PushNotificationMultiDispatcherInterface
{

    /**
     * Maximum number of endpoints allowed in one push.
     * @var integer
     */
    private const BATCH_SIZE = 1000;

    /**
     * Push Notification authentication token.
     * @var string
     */
    protected string $auth_token;

    /**
     * Shared instance of the Requests_Session class.
     * @var Requests_Session
     */
    protected Requests_Session $http;

    /**
     * Shared instance of a Logger class.
     *
     * @var LoggerInterface
     */
    protected LoggerInterface $logger;

    /**
     * Url to send the FCM push notification to.
     * @var string
     */
    private const GOOGLE_SEND_URL = 'https://fcm.googleapis.com/fcm/send';

    /**
     * Constructor.
     *
     * @param Requests_Session $http   Shared instance of the Requests_Session class.
     * @param LoggerInterface  $logger Shared instance of a Logger.
     */
    public function __construct(Requests_Session $http, LoggerInterface $logger)
    {
        $this->http       = $http;
        $this->logger     = $logger;
        $this->auth_token = '';
    }

    /**
     * Destructor.
     */
    public function __destruct()
    {
        unset($this->auth_token);
        unset($this->http);
        unset($this->logger);
    }

    /**
     * Getter for FCMResponse.
     *
     * @return FCMResponse
     */
    public function get_response(): FCMResponse
    {
        return new FCMResponse();
    }

    /**
     * Getter for FCMBatchResponse.
     *
     * @param Requests_Response $http_response Requests_Response object.
     * @param LoggerInterface   $logger        Shared instance of a Logger.
     * @param array             $endpoints     The endpoints the message was sent to (in the same order as sent).
     * @param string            $payload       Raw payload that was sent to FCM.
     *
     * @return FCMBatchResponse
     */
    public function get_batch_response(Requests_Response $http_response, LoggerInterface $logger, array $endpoints, string $payload): FCMBatchResponse
    {
        return new FCMBatchResponse($http_response, $logger, $endpoints, $payload);
    }

    /**
     * Push the notification.
     *
     * @param FCMPayload $payload   Payload object
     * @param array      $endpoints Endpoints to send to in this batch
     *
     * @return PushNotificationResponseInterface&FCMResponse Response object
     */
    public function push(object $payload, array &$endpoints): PushNotificationResponseInterface
    {
        $fcm_response = $this->get_response();

        foreach (array_chunk($endpoints, static::BATCH_SIZE) as &$batch)
        {
            $batch_response = $this->push_batch($payload, $batch);

            $fcm_response->add_batch_response($batch_response, $batch);

            unset($batch_response);
        }

        unset($batch);

        return $fcm_response;
    }

    /**
     * Push the notification to a batch of endpoints.
     *
     * @param FCMPayload $payload   Payload object
     * @param array      $endpoints Endpoints to send to in this batch
     *
     * @return FCMBatchResponse Response object
     */
    protected function push_batch(FCMPayload $payload, array &$endpoints)
    {
        $headers = [
            'Content-Type'  => 'application/json',
            'Authorization' => 'key=' . $this->auth_token,
        ];

        $tmp_payload = json_decode($payload->get_payload(), TRUE);

        if (count($endpoints) > 1)
        {
            $tmp_payload['registration_ids'] = $endpoints;
        }
        elseif (isset($endpoints[0]))
        {
            $tmp_payload['to'] = $endpoints[0];
        }

        $json_payload = json_encode($tmp_payload, JSON_UNESCAPED_UNICODE);

        try
        {
            $options = [
                'timeout'         => 15, // timeout in seconds
                'connect_timeout' => 15 // timeout in seconds
            ];

            $http_response = $this->http->post(static::GOOGLE_SEND_URL, $headers, $json_payload, $options);
        }
        catch (Requests_Exception $e)
        {
            $this->logger->warning(
                'Dispatching FCM notification(s) failed: {message}',
                [ 'message' => $e->getMessage() ]
            );
            $http_response = $this->get_new_response_object_for_failed_request();

            if ($e->getType() == 'curlerror' && curl_errno($e->getData()) == 28)
            {
                $http_response->status_code = 500;
            }
        }

        return $this->get_batch_response($http_response, $this->logger, $endpoints, $json_payload);
    }

    /**
     * Set the the auth token for the http headers.
     *
     * @param string $auth_token The auth token for the fcm push notifications
     *
     * @return FCMDispatcher Self reference
     */
    public function set_auth_token(string $auth_token): self
    {
        $this->auth_token = $auth_token;

        return $this;
    }

    /**
     * Get a Requests_Response object for a failed request.
     *
     * @return Requests_Response New instance of a Requests_Response object.
     */
    protected function get_new_response_object_for_failed_request(): Requests_Response
    {
        $http_response = new Requests_Response();

        $http_response->url = static::GOOGLE_SEND_URL;

        return $http_response;
    }

}

?>
