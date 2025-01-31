<?php

/**
 * This file contains the APNSResponseGetStatusTest class.
 *
 * @package    Lunr\Vortex\APNS\ApnsPHP
 * @author     Damien Tardy-Panis <damien@m2mobi.com>
 * @copyright  2016-2018, M2Mobi BV, Amsterdam, The Netherlands
 * @license    http://lunr.nl/LICENSE MIT License
 */

namespace Lunr\Vortex\APNS\ApnsPHP\Tests;

use Lunr\Vortex\APNS\ApnsPHP\APNSResponse;
use Lunr\Vortex\PushNotificationStatus;

use ReflectionClass;

/**
 * This class contains tests for the get_status function of the APNSResponse class.
 *
 * @covers Lunr\Vortex\APNS\ApnsPHP\APNSResponse
 */
class APNSResponseGetStatusTest extends APNSResponseTest
{

    /**
     * Testcase constructor.
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->class      = new APNSResponse($this->logger, [], [], [], '{}');
        $this->reflection = new ReflectionClass('Lunr\Vortex\APNS\ApnsPHP\APNSResponse');
    }

    /**
     * Unit test data provider.
     *
     * @return array $data array of endpoints statuses / status result
     */
    public function endpointDataProvider(): array
    {
        $data = [];

        $data['unknown status if no status set'] = [ [], PushNotificationStatus::UNKNOWN ];

        $data['unknown status if endpoint absent']           = [
            [
                'endpoint1' => PushNotificationStatus::INVALID_ENDPOINT,
            ],
            PushNotificationStatus::UNKNOWN,
        ];
        $data['unknown status if endpoint absent, full set'] = [
            [
                'endpoint1' => PushNotificationStatus::ERROR,
                'endpoint2' => PushNotificationStatus::INVALID_ENDPOINT,
                'endpoint3' => PushNotificationStatus::SUCCESS,
            ],
            PushNotificationStatus::UNKNOWN,
        ];

        $data['own status if present']           = [
            [
                'endpoint_param' => PushNotificationStatus::INVALID_ENDPOINT,
            ],
            PushNotificationStatus::INVALID_ENDPOINT,
        ];
        $data['own status if present, full set'] = [
            [
                'endpoint1'      => PushNotificationStatus::ERROR,
                'endpoint_param' => PushNotificationStatus::SUCCESS,
                'endpoint2'      => PushNotificationStatus::TEMPORARY_ERROR,
            ],
            PushNotificationStatus::SUCCESS,
        ];

        return $data;
    }

    /**
     * Test the get_status() behavior.
     *
     * @param array $statuses Endpoints statuses
     * @param int   $status   Expected function result
     *
     * @dataProvider endpointDataProvider
     * @covers       Lunr\Vortex\APNS\ApnsPHP\APNSResponse::get_status
     */
    public function testGetStatus($statuses, $status): void
    {
        $this->set_reflection_property_value('statuses', $statuses);

        $result = $this->class->get_status('endpoint_param');

        $this->assertEquals($status, $result);
    }

}
