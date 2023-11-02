<?php

/**
 * This file contains functionality to dispatch PAP Format Push Notifications.
 *
 * @package    Lunr\Vortex\PAP
 * @author     Heinz Wiesinger <heinz@m2mobi.com>
 * @author     Leonidas Diamantis <leonidas@m2mobi.com>
 * @copyright  2014-2018, M2Mobi BV, Amsterdam, The Netherlands
 * @license    http://lunr.nl/LICENSE MIT License
 */

namespace Lunr\Vortex\PAP;

use Lunr\Vortex\PushNotificationDispatcherInterface;
use Lunr\Vortex\PushNotificationResponseInterface;
use Requests_Exception;
use Requests_Response;

/**
 * PAP Format Push Notification Dispatcher.
 */
class PAPDispatcher implements PushNotificationDispatcherInterface
{

    /**
     * Push Notification authentication token.
     * @var string
     */
    private $auth_token;

    /**
     * Push Notification password.
     * @var string
     */
    private $password;

    /**
     * Push Notification content provider id.
     * @var string
     */
    private $cid;

    /**
     * Unique push identifier for each notification.
     * @var string
     */
    private $push_id;

    /**
     * Shared instance of the Requests_Session class.
     * @var \Requests_Session
     */
    private $http;

    /**
     * Shared instance of a Logger class.
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;

    /**
     * Boundary string for the PAP request.
     * @var string
     */
    const PAP_BOUNDARY = 'mPsbVQo0a68eIL3OAxnm';

    /**
     * Constructor.
     *
     * @param \Requests_Session        $http   Shared instance of the Requests_Session class.
     * @param \Psr\Log\LoggerInterface $logger Shared instance of a Logger.
     */
    public function __construct($http, $logger)
    {
        $this->auth_token = '';
        $this->password   = '';
        $this->cid        = '';
        $this->push_id    = '';
        $this->http       = $http;
        $this->logger     = $logger;
    }

    /**
     * Destructor.
     */
    public function __destruct()
    {
        unset($this->auth_token);
        unset($this->password);
        unset($this->cid);
        unset($this->push_id);
        unset($this->http);
        unset($this->logger);
    }

    /**
     * Push the notification.
     *
     * @param PAPPayload $payload   Payload object
     * @param array      $endpoints Endpoints to send to in this batch
     *
     * @return PushNotificationResponseInterface&PAPResponse Response object
     */
    public function push(object $payload, array &$endpoints): PushNotificationResponseInterface
    {
        // construct PAP URL
        $pap_url = "https://cp{$this->cid}.pushapi.na.blackberry.com/mss/PD_pushRequest";

        $pap_data = $this->construct_pap_data($payload, $endpoints[0]);

        $options = [
            'auth' => [
                $this->auth_token,
                $this->password,
            ],
        ];

        $headers = [
            'Content-Type' => 'multipart/related; boundary=' . self::PAP_BOUNDARY . '; type=application/xml',
            'Accept'       => 'text/html, image/gif, image/jpeg, *; q=.2, */*; q=.2',
            'Connection'   => 'keep-alive',
        ];

        try
        {
            $response = $this->http->post($pap_url, $headers, $pap_data, $options);
        }
        catch (Requests_Exception $e)
        {
            $response = $this->get_new_response_object_for_failed_request();
            $context  = [ 'error' => $e->getMessage(), 'endpoint' => $endpoints[0] ];

            $this->logger->warning('Dispatching PAP notification to {endpoint} failed: {error}', $context);
        }

        $this->push_id = '';

        return new PAPResponse($response, $this->logger, $endpoints[0], $pap_data);
    }

    /**
     * Constructs the control XML of the PAP request.
     *
     * @param PAPPayload $payload  Payload object
     * @param string     $endpoint Endpoint to send to
     *
     * @return string The control XML populated with all relevant values
     */
    protected function construct_pap_control_xml($payload, $endpoint)
    {
        // construct PAP control xml
        // @codingStandardsIgnoreStart
        $xml  = "<?xml version=\"1.0\"?>\n";
        $xml .= "<!DOCTYPE pap PUBLIC \"-//WAPFORUM//DTD PAP 2.1//EN\" \"http://www.openmobilealliance.org/tech/DTD/pap_2.1.dtd\">\n";
        $xml .= "<pap>\n";
        $xml .= "<push-message push-id=\"{$this->push_id}\" source-reference=\"{$this->auth_token}\" deliver-before-timestamp=\"{$payload->get_priority()}\">\n";
        $xml .= "<address address-value=\"$endpoint\"/>\n";
        $xml .= "<quality-of-service delivery-method=\"unconfirmed\"/>\n";
        $xml .= "</push-message>\n</pap>\n";
        // @codingStandardsIgnoreEnd

        return $xml;
    }

    /**
     * Constructs the full data of the PAP request.
     *
     * @param PAPPayload $payload  Payload object
     * @param string     $endpoint Endpoint to send to
     *
     * @return string The PAP request data populated with all relevant values
     */
    protected function construct_pap_data($payload, $endpoint)
    {
        $this->push_id = $endpoint . microtime(TRUE);

        // inject the unique push id in the payload
        $tmp_payload       = json_decode($payload->get_payload(), TRUE);
        $tmp_payload['id'] = $this->push_id;

        // construct custom headers; inject control xml & payload
        $data  = '--' . self::PAP_BOUNDARY . "\r\n";
        $data .= "Content-Type: application/xml; charset=UTF-8\r\n\r\n";
        $data .= $this->construct_pap_control_xml($payload, $endpoint) . "\r\n--";
        $data .= self::PAP_BOUNDARY . "\r\n";
        $data .= "Content-Type: text/plain\r\n";
        $data .= 'Push-Message-ID: ' . $this->push_id . "\r\n\r\n";
        $data .= json_encode($tmp_payload) . "\r\n--";
        $data .= self::PAP_BOUNDARY . "--\n\r";

        return $data;
    }

    /**
     * Set the auth token for the http headers.
     *
     * @param string $auth_token The auth token for the PAP push notifications
     *
     * @return PAPDispatcher Self reference
     */
    public function set_auth_token($auth_token)
    {
        $this->auth_token = $auth_token;

        return $this;
    }

    /**
     * Set the password for the push service.
     *
     * @param string $password The password of the push service
     *
     * @return PAPDispatcher Self reference
     */
    public function set_password($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Set the content provider id for the push service.
     *
     * @param string $cid The content provider id for the PAP push notifications
     *
     * @return PAPDispatcher Self reference
     */
    public function set_content_provider_id($cid)
    {
        $this->cid = $cid;

        return $this;
    }

    /**
     * Get a Requests_Response object for a failed request.
     *
     * @return \Requests_Response New instance of a Requests_Response object.
     */
    protected function get_new_response_object_for_failed_request()
    {
        $http_response = new Requests_Response();

        $http_response->url = "https://cp{$this->cid}.pushapi.na.blackberry.com/mss/PD_pushRequest";

        return $http_response;
    }

}

?>
