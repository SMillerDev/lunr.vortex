<?php

/**
 * This file contains the WNSResponseSetTest class.
 *
 * SPDX-FileCopyrightText: Copyright 2013 M2mobi B.V., Amsterdam, The Netherlands
 * SPDX-FileCopyrightText: Copyright 2022 Move Agency Group B.V., Zwolle, The Netherlands
 * SPDX-License-Identifier: MIT
 */

namespace Lunr\Vortex\WNS\Tests;

use Lunr\Vortex\PushNotificationStatus;

/**
 * This class contains tests for setting meta information about WNS dispatches.
 *
 * @covers Lunr\Vortex\WNS\WNSResponse
 */
class WNSResponseSetTest extends WNSResponseTest
{

    /**
     * Testcase Constructor.
     */
    public function setUp(): void
    {
        parent::setUpSuccess();
    }

    /**
     * Test setting the status for a successful request.
     *
     * @covers Lunr\Vortex\WNS\WNSResponse::set_status
     */
    public function testStatusForSuccessRequestStatus(): void
    {
        $method = $this->get_accessible_reflection_method('set_status');
        $method->invokeArgs($this->class, [ 'URL', $this->logger ]);

        $this->logger->expects($this->never())
                     ->method('warning');

        $this->assertEquals(PushNotificationStatus::Success, $this->get_reflection_property_value('status'));
    }

    /**
     * Test setting the status for a failed request.
     *
     * @param int    $code     Status code
     * @param string $nstatus  Notification status string
     * @param int    $expected Expected push notification status
     *
     * @dataProvider failedRequestProvider
     * @covers       Lunr\Vortex\WNS\WNSResponse::set_status
     */
    public function testSetStatusForNonSuccessRequestStatus($code, $nstatus, $expected): void
    {
        $headers = [];

        $headers['X-WNS-Status']                 = $nstatus;
        $headers['X-WNS-DeviceConnectionStatus'] = 'N/A';
        $headers['X-WNS-Error-Description']      = 'Something is broken';
        $headers['X-WNS-Debug-Trace']            = 'Tracing brokenness';

        $this->set_reflection_property_value('headers', $headers);
        $this->set_reflection_property_value('http_code', $code);

        $context = [
            'endpoint'          => 'URL',
            'nstatus'           => $nstatus,
            'dstatus'           => 'N/A',
            'error_description' => 'Something is broken',
            'error_trace'       => 'Tracing brokenness',
        ];

        $message  = 'Push notification delivery status for endpoint {endpoint}: ';
        $message .= '{nstatus}, device {dstatus}, description {error_description}, trace {error_trace}';

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
