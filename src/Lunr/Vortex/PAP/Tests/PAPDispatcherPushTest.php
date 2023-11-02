<?php

/**
 * This file contains the PAPDispatcherPushTest class.
 *
 * @package    Lunr\Vortex\PAP
 * @author     Heinz Wiesinger <heinz@m2mobi.com>
 * @author     Leonidas Diamantis <leonidas@m2mobi.com>
 * @copyright  2014-2018, M2Mobi BV, Amsterdam, The Netherlands
 * @license    http://lunr.nl/LICENSE MIT License
 */

namespace Lunr\Vortex\PAP\Tests;

use Requests_Exception;

/**
 * This class contains test for the push() method of the PAPDispatcher class.
 *
 * @covers Lunr\Vortex\PAP\PAPDispatcher
 */
class PAPDispatcherPushTest extends PAPDispatcherTest
{

    /**
     * Test that push() returns PAPResponseObject.
     *
     * @covers   Lunr\Vortex\PAP\PAPDispatcher::push
     */
    public function testPushReturnsPAPResponseObjectOnRequestFailure(): void
    {
        $this->mock_function('microtime', function () { return 12345; });

        $this->set_reflection_property_value('auth_token', 'auth_token');
        $this->set_reflection_property_value('password', 'password');
        $this->set_reflection_property_value('cid', 'cid');
        $this->set_reflection_property_value('push_id', '12345');

        $this->payload->expects($this->once())
                      ->method('get_priority')
                      ->willReturn('deliverbefore');

        $endpoints = [ 'endpoint' ];

        $headers = [
            'Content-Type' => 'multipart/related; boundary=mPsbVQo0a68eIL3OAxnm; type=application/xml',
            'Accept'       => 'text/html, image/gif, image/jpeg, *; q=.2, */*; q=.2',
            'Connection'   => 'keep-alive',
        ];

        $url = 'https://cpcid.pushapi.na.blackberry.com/mss/PD_pushRequest';

        $xml = file_get_contents(TEST_STATICS . '/Vortex/pap/request_custom_headers.txt');

        $options = [
            'auth' => [
                'auth_token',
                'password',
            ],
        ];

        $this->payload->expects($this->once())
                      ->method('get_payload')
                      ->willReturn('{"message":"test"}');

        $this->http->expects($this->once())
                   ->method('post')
                   ->with($url, $headers, $xml, $options)
                   ->will($this->throwException(new Requests_Exception('Network error!', 'curlerror', NULL)));

        $message = 'Dispatching PAP notification to {endpoint} failed: {error}';
        $context = [ 'endpoint' => 'endpoint', 'error' => 'Network error!' ];

        $this->logger->expects($this->exactly(2))
                     ->method('warning')
                     ->withConsecutive([ $message, $context ], [ 'Parsing response of PAP notification to {endpoint} failed: {error}' ]);

        $this->assertInstanceOf('Lunr\Vortex\PAP\PAPResponse', $this->class->push($this->payload, $endpoints));

        $this->unmock_function('microtime');
    }

    /**
     * Test that push() returns PAPResponseObject.
     *
     * @covers   Lunr\Vortex\PAP\PAPDispatcher::push
     */
    public function testPushReturnsPAPResponseObject(): void
    {
        $this->mock_function('microtime', function () { return 12345; });

        $this->set_reflection_property_value('auth_token', 'auth_token');
        $this->set_reflection_property_value('password', 'password');
        $this->set_reflection_property_value('cid', 'cid');
        $this->set_reflection_property_value('push_id', '12345');

        $this->payload->expects($this->once())
                      ->method('get_priority')
                      ->willReturn('deliverbefore');

        $endpoints = [ 'endpoint' ];

        $headers = [
            'Content-Type' => 'multipart/related; boundary=mPsbVQo0a68eIL3OAxnm; type=application/xml',
            'Accept'       => 'text/html, image/gif, image/jpeg, *; q=.2, */*; q=.2',
            'Connection'   => 'keep-alive',
        ];

        $url = 'https://cpcid.pushapi.na.blackberry.com/mss/PD_pushRequest';

        $xml = file_get_contents(TEST_STATICS . '/Vortex/pap/request_custom_headers.txt');

        $options = [
            'auth' => [
                'auth_token',
                'password',
            ],
        ];

        $this->payload->expects($this->once())
                      ->method('get_payload')
                      ->willReturn('{"message":"test"}');

        $this->http->expects($this->once())
                   ->method('post')
                   ->with($url, $headers, $xml, $options)
                   ->willReturn($this->response);

        $this->response->status_code = 200;
        $this->response->body        = file_get_contents(TEST_STATICS . '/Vortex/pap/response.xml');

        $this->assertInstanceOf('Lunr\Vortex\PAP\PAPResponse', $this->class->push($this->payload, $endpoints));

        $this->unmock_function('microtime');
    }

    /**
     * Test that push() resets the properties after a push.
     *
     * @covers   Lunr\Vortex\PAP\PAPDispatcher::push
     */
    public function testPushResetsPropertiesOnRequestFailure(): void
    {
        $this->mock_function('microtime', function () { return 12345; });

        $this->set_reflection_property_value('auth_token', 'auth_token');
        $this->set_reflection_property_value('password', 'password');
        $this->set_reflection_property_value('cid', 'cid');
        $this->set_reflection_property_value('push_id', 'endpoint12345');

        $this->payload->expects($this->once())
                      ->method('get_priority')
                      ->willReturn('deliverbefore');

        $endpoints = [ 'endpoint' ];

        $headers = [
            'Content-Type' => 'multipart/related; boundary=mPsbVQo0a68eIL3OAxnm; type=application/xml',
            'Accept'       => 'text/html, image/gif, image/jpeg, *; q=.2, */*; q=.2',
            'Connection'   => 'keep-alive',
        ];

        $url = 'https://cpcid.pushapi.na.blackberry.com/mss/PD_pushRequest';

        $xml = file_get_contents(TEST_STATICS . '/Vortex/pap/request_custom_headers.txt');

        $options = [
            'auth' => [
                'auth_token',
                'password',
            ],
        ];

        $this->payload->expects($this->once())
                      ->method('get_payload')
                      ->willReturn('{"message":"test"}');

        $this->http->expects($this->once())
                   ->method('post')
                   ->with($url, $headers, $xml, $options)
                   ->will($this->throwException(new Requests_Exception('Network error!', 'curlerror', NULL)));

        $message = 'Dispatching PAP notification to {endpoint} failed: {error}';
        $context = [ 'endpoint' => 'endpoint', 'error' => 'Network error!' ];

        $this->logger->expects($this->exactly(2))
                     ->method('warning')
                     ->withConsecutive([ $message, $context ], [ 'Parsing response of PAP notification to {endpoint} failed: {error}' ]);

        $this->class->push($this->payload, $endpoints);

        $this->assertPropertyEquals('push_id', '');

        $this->unmock_function('microtime');
    }

    /**
     * Test that push() resets the properties after a push.
     *
     * @covers   Lunr\Vortex\PAP\PAPDispatcher::push
     */
    public function testPushResetsProperties(): void
    {
        $this->mock_function('microtime', function () { return 12345; });

        $this->set_reflection_property_value('auth_token', 'auth_token');
        $this->set_reflection_property_value('password', 'password');
        $this->set_reflection_property_value('cid', 'cid');
        $this->set_reflection_property_value('push_id', 'endpoint12345');

        $this->payload->expects($this->once())
                      ->method('get_priority')
                      ->willReturn('deliverbefore');

        $endpoints = [ 'endpoint' ];

        $headers = [
            'Content-Type' => 'multipart/related; boundary=mPsbVQo0a68eIL3OAxnm; type=application/xml',
            'Accept'       => 'text/html, image/gif, image/jpeg, *; q=.2, */*; q=.2',
            'Connection'   => 'keep-alive',
        ];

        $url = 'https://cpcid.pushapi.na.blackberry.com/mss/PD_pushRequest';

        $xml = file_get_contents(TEST_STATICS . '/Vortex/pap/request_custom_headers.txt');

        $options = [
            'auth' => [
                'auth_token',
                'password',
            ],
        ];

        $this->payload->expects($this->once())
                      ->method('get_payload')
                      ->willReturn('{"message":"test"}');

        $this->http->expects($this->once())
                   ->method('post')
                   ->with($url, $headers, $xml, $options)
                   ->willReturn($this->response);

        $this->class->push($this->payload, $endpoints);

        $this->assertPropertyEquals('push_id', '');

        $this->unmock_function('microtime');
    }

}

?>
