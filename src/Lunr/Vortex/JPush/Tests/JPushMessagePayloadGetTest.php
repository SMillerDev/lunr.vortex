<?php

/**
 * This file contains the JPushMessagePayloadGetTest class.
 *
 * @package    Lunr\Vortex\JPush
 * @author     Sean Molenaar <s.molenaar@m2mobi.com>
 * @copyright  2020, M2Mobi BV, Amsterdam, The Netherlands
 * @license    http://lunr.nl/LICENSE MIT License
 */

namespace Lunr\Vortex\JPush\Tests;

/**
 * This class contains tests for the getters of the JPushPayload class.
 *
 * @covers \Lunr\Vortex\JPush\JPushPayload
 */
class JPushMessagePayloadGetTest extends JPushMessagePayloadTest
{

    /**
     * Test message get_payload() with everything being present.
     *
     * @covers \Lunr\Vortex\JPush\JPushMessagePayload::get_payload
     */
    public function testGetPayloadMessage(): void
    {
        $elements = [
            'registration_id' => [ 'one', 'two', 'three' ],
            'message'    => [
                'title' => 'title'
            ],
            'notification'    => [
                'android' => [
                    'alert' => 'a'
                ],
                'ios' => [
                    'alert' => 'a'
                ],
            ],
            'time_to_live'     => 10,
        ];
        $expected = [
            'registration_id' => [ 'one', 'two', 'three' ],
            'message'    => [
                'title' => 'title'
            ],
            'time_to_live'     => 10,
        ];

        $this->set_reflection_property_value('elements', $elements);

        $this->assertEquals($expected, $this->class->get_payload());
    }

}

?>
