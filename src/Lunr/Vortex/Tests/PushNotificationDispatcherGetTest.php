<?php

/**
 * This file contains the PushNotificationDispatcherGetTest class.
 *
 * SPDX-FileCopyrightText: Copyright 2024 Move Agency Group B.V., Zwolle, The Netherlands
 * SPDX-License-Identifier: MIT
 */

namespace Lunr\Vortex\Tests;

use Lunr\Vortex\PushNotificationStatus;

/**
 * This class contains tests for the get_response_status function of the PushNotificationDispatcher class.
 *
 * @covers Lunr\Vortex\PushNotificationDispatcher
 */
class PushNotificationDispatcherGetTest extends PushNotificationDispatcherTest
{

    /**
     * Test that get_endpoints_by_status ignores unset statuses.
     *
     * @covers Lunr\Vortex\PushNotificationDispatcher::get_endpoints_by_status
     */
    public function testGetEndpointsByStatusIgnoresUnsetStatuses(): void
    {
        $statuses = [
            PushNotificationStatus::Success->value => [
                [
                    '1',
                    'i',
                ],
            ],
        ];

        $value = $this->class->get_endpoints_by_status([ PushNotificationStatus::Success->value ]);

        $this->assertSame([], $value);
    }

    /**
     * Test that get_endpoints_by_status returns the endpoint/platform pair(s) for one status code.
     *
     * @covers Lunr\Vortex\PushNotificationDispatcher::get_endpoints_by_status
     */
    public function testGetEndpointsByStatusReturnsEndpointsForOneCode(): void
    {
        $statuses = [
            PushNotificationStatus::Success->value => [
                [
                    '1',
                    'i',
                ],
            ],
        ];

        $this->set_reflection_property_value('statuses', $statuses);

        $expected = [
            [
                '1',
                'i',
            ],
        ];

        $value = $this->class->get_endpoints_by_status([ PushNotificationStatus::Success->value ]);

        $this->assertEquals($expected, $value);
    }

    /**
     * Test that get_endpoints_by_status returns the endpoint/platform pair(s) for multiple status codes.
     *
     * @covers Lunr\Vortex\PushNotificationDispatcher::get_endpoints_by_status
     */
    public function testGetEndpointsByStatusReturnsEndpointsForMultipleCodes(): void
    {
        $statuses = [
            PushNotificationStatus::ClientError->value => [
                [
                    '1',
                    'i',
                ],
            ],
            PushNotificationStatus::Error->value => [
                [
                    '2',
                    'a',
                ],
            ],
        ];

        $this->set_reflection_property_value('statuses', $statuses);

        $expected = [
            [
                '1',
                'i',
            ],
            [
                '2',
                'a',
            ],
        ];

        $value = $this->class->get_endpoints_by_status([ PushNotificationStatus::ClientError->value, PushNotificationStatus::Error->value ]);

        $this->assertEquals($expected, $value);
    }

    /**
     * Test that get_endpoints_by_status returns empty array for no status codes.
     *
     * @covers Lunr\Vortex\PushNotificationDispatcher::get_endpoints_by_status
     */
    public function testGetEndpointsByStatusReturnsEmptyArrayForNoCodes(): void
    {
        $statuses = [
            PushNotificationStatus::ClientError->value => [
                [
                    '1',
                    'i',
                ],
            ],
            PushNotificationStatus::Error->value => [
                [
                    '2',
                    'a',
                ],
            ],
        ];

        $this->set_reflection_property_value('statuses', $statuses);

        $this->assertEquals([], $this->class->get_endpoints_by_status([]));
    }

    /**
     * Test that get_statuses() returns the entire status array.
     *
     * @covers Lunr\Vortex\PushNotificationDispatcher::get_statuses
     */
    public function testGetStatusesReturnsStatuses(): void
    {
        $statuses = [
            PushNotificationStatus::ClientError->value => [
                [
                    '1',
                    'i',
                ],
            ],
            PushNotificationStatus::Error->value => [
                [
                    '2',
                    'a',
                ],
            ],
        ];

        $this->set_reflection_property_value('statuses', $statuses);

        $this->assertSame($statuses, $this->class->get_statuses());
    }

}

?>
