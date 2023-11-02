<?php

/**
 * This file contains the MPNSResponseSetTest class.
 *
 * @package    Lunr\Vortex\MPNS
 * @author     Heinz Wiesinger <heinz@m2mobi.com>
 * @copyright  2013-2018, M2Mobi BV, Amsterdam, The Netherlands
 * @license    http://lunr.nl/LICENSE MIT License
 */

namespace Lunr\Vortex\MPNS\Tests;

use Lunr\Vortex\PushNotificationStatus;

/**
 * This class contains tests for setting meta information about MPNS dispatches.
 *
 * @covers Lunr\Vortex\MPNS\MPNSResponse
 */
class MPNSResponseSetTest extends MPNSResponseTest
{

    /**
     * Testcase Constructor.
     */
    public function setUp(): void
    {
        parent::setUpSuccess();
    }

    /**
     * Test setting headers for status 412.
     *
     * @covers Lunr\Vortex\MPNS\MPNSResponse::set_headers
     */
    public function testSetHeadersWithPreconditionFailedStatus(): void
    {
        $in_headers = [
            'Date'                     => '2013-07-05',
            'X-Notificationstatus'     => 'Received',
            'X-Deviceconnectionstatus' => 'Connected',
            'X-Subscriptionstatus'     => 'Active',
        ];

        $this->set_reflection_property_value('http_code', 412);

        $method = $this->get_accessible_reflection_method('set_headers');
        $method->invokeArgs($this->class, [ new \Requests_Response_Headers($in_headers) ]);

        $headers = $this->get_reflection_property_value('headers');

        $this->assertArrayHasKey('X-Notificationstatus', $headers);
        $this->assertArrayHasKey('X-Deviceconnectionstatus', $headers);
        $this->assertArrayHasKey('X-Subscriptionstatus', $headers);

        $this->assertEquals('Received', $headers['X-Notificationstatus']);
        $this->assertEquals('Connected', $headers['X-Deviceconnectionstatus']);
        $this->assertEquals('N/A', $headers['X-Subscriptionstatus']);
    }

    /**
     * Test setting headers for special statuses that don't have optional headers.
     *
     * @param int $status Special status code
     *
     * @dataProvider specialStatusProvider
     * @covers       \Lunr\Vortex\MPNS\MPNSResponse::set_headers
     */
    public function testSetHeadersWithSpecialStatusCodes(int $status): void
    {
        $in_headers = [
            'Date'                     => '2013-07-05',
            'X-Notificationstatus'     => 'Received',
            'X-Deviceconnectionstatus' => 'Connected',
            'X-Subscriptionstatus'     => 'Active',
        ];

        $this->set_reflection_property_value('http_code', $status);

        $method = $this->get_accessible_reflection_method('set_headers');
        $method->invokeArgs($this->class, [ new \Requests_Response_Headers($in_headers) ]);

        $headers = $this->get_reflection_property_value('headers');

        $this->assertArrayHasKey('X-Notificationstatus', $headers);
        $this->assertArrayHasKey('X-Deviceconnectionstatus', $headers);
        $this->assertArrayHasKey('X-Subscriptionstatus', $headers);

        $this->assertEquals('N/A', $headers['X-Notificationstatus']);
        $this->assertEquals('N/A', $headers['X-Deviceconnectionstatus']);
        $this->assertEquals('N/A', $headers['X-Subscriptionstatus']);
    }

    /**
     * Test setting the status for a successful request.
     *
     * @covers Lunr\Vortex\MPNS\MPNSResponse::set_status
     */
    public function testStatusForSuccessRequestStatus(): void
    {
        $method = $this->get_accessible_reflection_method('set_status');
        $method->invokeArgs($this->class, [ 'URL', $this->logger ]);

        $this->logger->expects($this->never())
                     ->method('warning');

        $this->assertEquals(PushNotificationStatus::SUCCESS, $this->get_reflection_property_value('status'));
    }

    /**
     * Test setting the status for a failed request.
     *
     * @param int    $code     Status code
     * @param string $nstatus  Notification status string
     * @param int    $expected Expected push notification status
     *
     * @dataProvider failedRequestProvider
     * @covers       Lunr\Vortex\MPNS\MPNSResponse::set_status
     */
    public function testSetStatusForNonSuccessRequestStatus($code, $nstatus, $expected): void
    {
        $headers = [];

        $headers['X-Notificationstatus']     = $nstatus;
        $headers['X-Deviceconnectionstatus'] = 'N/A';
        $headers['X-Subscriptionstatus']     = 'N/A';

        $this->set_reflection_property_value('headers', new \Requests_Response_Headers($headers));
        $this->set_reflection_property_value('http_code', $code);

        $context = [
            'endpoint' => 'URL',
            'nstatus'  => $nstatus,
            'dstatus'  => 'N/A',
            'sstatus'  => 'N/A',
        ];

        $message  = 'MPNS notification delivery status for endpoint {endpoint}: ';
        $message .= '{nstatus}, device {dstatus}, subscription {sstatus}';

        $this->logger->expects($this->once())
                     ->method('warning')
                     ->with(
                         $this->equalTo($message),
                         $this->equalTo($context)
                     );

        $method = $this->get_accessible_reflection_method('set_status');
        $method->invokeArgs($this->class, [ 'URL', $this->logger ]);

        $this->assertEquals($expected, $this->get_reflection_property_value('status'));
    }

}

?>
