<?php

/**
 * This file contains the WNSDispatcherPushTest class.
 *
 * @package    Lunr\Vortex\WNS
 * @author     Sean Molenaar <sean@m2mobi.com>
 * @copyright  2013-2018, M2Mobi BV, Amsterdam, The Netherlands
 * @license    http://lunr.nl/LICENSE MIT License
 */

namespace Lunr\Vortex\WNS\Tests;

use Lunr\Vortex\WNS\WNSType;
use Requests_Exception;

/**
 * This class contains test for the push() method of the WNSDispatcher class.
 *
 * @covers Lunr\Vortex\WNS\WNSDispatcher
 */
class WNSDispatcherPushTest extends WNSDispatcherTest
{

    /**
     * Test that the response will be null if no authentication is done.
     *
     * @covers Lunr\Vortex\WNS\WNSDispatcher::push
     */
    public function testPushingWithoutOauthReturnsWNSResponse(): void
    {
        $endpoints = [ 'endpoint' ];

        $message = 'Tried to push WNS notification to {endpoint} but wasn\'t authenticated.';
        $context = [ 'endpoint' => 'endpoint' ];

        $this->logger->expects($this->once())
                     ->method('warning')
                     ->with($message, $context);

        $this->http->expects($this->never())
                   ->method('post');

        $this->assertInstanceOf('\Lunr\Vortex\WNS\WNSResponse', $this->class->push($this->payload, $endpoints));
    }

    /**
     * Test that push() resets the properties after a push.
     *
     * @covers Lunr\Vortex\WNS\WNSDispatcher::push
     */
    public function testPushWithoutOauthResetsProperties(): void
    {
        $this->set_reflection_property_value('type', WNSType::TOAST);

        $endpoints = [ 'endpoint' ];

        $message = 'Tried to push WNS notification to {endpoint} but wasn\'t authenticated.';
        $context = [ 'endpoint' => 'endpoint' ];

        $this->logger->expects($this->once())
                     ->method('warning')
                     ->with($message, $context);

        $this->http->expects($this->never())
                   ->method('post');

        $this->class->push($this->payload, $endpoints);

        $this->assertPropertySame('type', WNSType::RAW);
    }

    /**
     * Test that pushing a Tile notification sets the X-WNS-Type header.
     *
     * @covers Lunr\Vortex\WNS\WNSDispatcher::push
     */
    public function testPushingTileSetsTargetHeader(): void
    {
        $this->payload = $this->getMockBuilder('Lunr\Vortex\WNS\WNSTilePayload')
                              ->disableOriginalConstructor()
                              ->getMock();

        $this->set_reflection_property_value('oauth_token', '123456');

        $endpoints = [ 'endpoint' ];

        $headers = [
            'X-WNS-Type'             => 'wns/tile',
            'Accept'                 => 'application/*',
            'Authorization'          => 'Bearer 123456',
            'X-WNS-RequestForStatus' => 'true',
            'Content-Type'           => 'text/xml',
        ];

        $this->payload->expects($this->once())
                      ->method('get_payload')
                      ->willReturn('payload');

        $this->http->expects($this->once())
                   ->method('post')
                   ->with('endpoint', $headers, 'payload')
                   ->willReturn($this->response);

        $this->class->push($this->payload, $endpoints);
    }

    /**
     * Test that pushing a Toast notification sets the X-WNS-Type header.
     *
     * @covers Lunr\Vortex\WNS\WNSDispatcher::push
     */
    public function testPushingToastSetsTargetHeader(): void
    {
        $this->payload = $this->getMockBuilder('Lunr\Vortex\WNS\WNSToastPayload')
                              ->disableOriginalConstructor()
                              ->getMock();

        $this->set_reflection_property_value('oauth_token', '123456');

        $endpoints = [ 'endpoint' ];

        $headers = [
            'X-WNS-Type'             => 'wns/toast',
            'Accept'                 => 'application/*',
            'Authorization'          => 'Bearer 123456',
            'X-WNS-RequestForStatus' => 'true',
            'Content-Type'           => 'text/xml',
        ];

        $this->payload->expects($this->once())
                      ->method('get_payload')
                      ->willReturn('payload');

        $this->http->expects($this->once())
                   ->method('post')
                   ->with('endpoint', $headers, 'payload')
                   ->willReturn($this->response);

        $this->class->push($this->payload, $endpoints);
    }

    /**
     * Test that pushing a Raw notification does not set the X-WNS-Type header.
     *
     * @covers Lunr\Vortex\WNS\WNSDispatcher::push
     */
    public function testPushingRawDoesNotSetTargetHeader(): void
    {
        $this->set_reflection_property_value('oauth_token', '123456');

        $endpoints = [ 'endpoint' ];

        $headers = [
            'X-WNS-Type'             => 'wns/raw',
            'Accept'                 => 'application/*',
            'Authorization'          => 'Bearer 123456',
            'X-WNS-RequestForStatus' => 'true',
            'Content-Type'           => 'application/octet-stream',
        ];

        $this->payload->expects($this->once())
                      ->method('get_payload')
                      ->willReturn('payload');

        $this->http->expects($this->once())
                   ->method('post')
                   ->with('endpoint', $headers, 'payload')
                   ->willReturn($this->response);

        $this->class->push($this->payload, $endpoints);
    }

    /**
     * Test that push() returns WNSResponseObject.
     *
     * @covers Lunr\Vortex\WNS\WNSDispatcher::push
     */
    public function testPushReturnsWNSResponseObjectOnRequestFailure(): void
    {
        $this->set_reflection_property_value('oauth_token', '123456');

        $endpoints = [ 'endpoint' ];

        $headers = [
            'X-WNS-Type'             => 'wns/raw',
            'Accept'                 => 'application/*',
            'Authorization'          => 'Bearer 123456',
            'X-WNS-RequestForStatus' => 'true',
            'Content-Type'           => 'application/octet-stream',
        ];

        $this->payload->expects($this->once())
                      ->method('get_payload')
                      ->willReturn('payload');

        $this->http->expects($this->once())
                   ->method('post')
                   ->with('endpoint', $headers, 'payload')
                   ->will($this->throwException(new Requests_Exception('Network error!', 'curlerror', NULL)));

        $message = 'Dispatching WNS notification to {endpoint} failed: {error}';
        $context = [ 'endpoint' => 'endpoint', 'error' => 'Network error!' ];

        $this->logger->expects($this->once())
                     ->method('warning')
                     ->with($message, $context);

        $this->assertInstanceOf('Lunr\Vortex\WNS\WNSResponse', $this->class->push($this->payload, $endpoints));
    }

    /**
     * Test that push() returns WNSResponseObject.
     *
     * @covers Lunr\Vortex\WNS\WNSDispatcher::push
     */
    public function testPushReturnsWNSResponseObject(): void
    {
        $this->set_reflection_property_value('oauth_token', '123456');

        $endpoints = [ 'endpoint' ];

        $headers = [
            'X-WNS-Type'             => 'wns/raw',
            'Accept'                 => 'application/*',
            'Authorization'          => 'Bearer 123456',
            'X-WNS-RequestForStatus' => 'true',
            'Content-Type'           => 'application/octet-stream',
        ];

        $this->payload->expects($this->once())
                      ->method('get_payload')
                      ->willReturn('payload');

        $this->http->expects($this->once())
                   ->method('post')
                   ->with('endpoint', $headers, 'payload')
                   ->willReturn($this->response);

        $this->assertInstanceOf('Lunr\Vortex\WNS\WNSResponse', $this->class->push($this->payload, $endpoints));
    }

    /**
     * Test that push() resets the properties after a push.
     *
     * @covers Lunr\Vortex\WNS\WNSDispatcher::push
     */
    public function testPushResetsPropertiesOnRequestFailure(): void
    {
        $this->payload = $this->getMockBuilder('Lunr\Vortex\WNS\WNSToastPayload')
                              ->disableOriginalConstructor()
                              ->getMock();

        $this->set_reflection_property_value('oauth_token', '123456');

        $endpoints = [ 'endpoint' ];

        $headers = [
            'X-WNS-Type'             => 'wns/toast',
            'Accept'                 => 'application/*',
            'Authorization'          => 'Bearer 123456',
            'X-WNS-RequestForStatus' => 'true',
            'Content-Type'           => 'text/xml',
        ];

        $this->payload->expects($this->once())
                      ->method('get_payload')
                      ->willReturn('payload');

        $this->http->expects($this->once())
                   ->method('post')
                   ->with('endpoint', $headers, 'payload')
                   ->will($this->throwException(new Requests_Exception('Network error!', 'curlerror', NULL)));

        $message = 'Dispatching WNS notification to {endpoint} failed: {error}';
        $context = [ 'endpoint' => 'endpoint', 'error' => 'Network error!' ];

        $this->logger->expects($this->once())
                     ->method('warning')
                     ->with($message, $context);

        $this->class->push($this->payload, $endpoints);
        $this->assertPropertySame('type', WNSType::RAW);
    }

    /**
     * Test that push() resets the properties after a push.
     *
     * @covers Lunr\Vortex\WNS\WNSDispatcher::push
     */
    public function testPushResetsProperties(): void
    {
        $this->payload = $this->getMockBuilder('Lunr\Vortex\WNS\WNSToastPayload')
                              ->disableOriginalConstructor()
                              ->getMock();

        $this->set_reflection_property_value('oauth_token', '123456');

        $endpoints = [ 'endpoint' ];

        $headers = [
            'X-WNS-Type'             => 'wns/toast',
            'Accept'                 => 'application/*',
            'Authorization'          => 'Bearer 123456',
            'X-WNS-RequestForStatus' => 'true',
            'Content-Type'           => 'text/xml',
        ];

        $this->payload->expects($this->once())
                      ->method('get_payload')
                      ->willReturn('payload');

        $this->http->expects($this->once())
                   ->method('post')
                   ->with('endpoint', $headers, 'payload')
                   ->willReturn($this->response);

        $this->class->push($this->payload, $endpoints);

        $this->assertPropertySame('type', WNSType::RAW);
    }

}

?>
