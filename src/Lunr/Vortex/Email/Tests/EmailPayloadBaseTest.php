<?php

/**
 * This file contains the EmailPayloadBaseTest class.
 *
 * @package    Lunr\Vortex\Email
 * @author     Leonidas Diamantis <leonidas@m2mobi.com>
 * @copyright  2014-2018, M2Mobi BV, Amsterdam, The Netherlands
 * @license    http://lunr.nl/LICENSE MIT License
 */

namespace Lunr\Vortex\Email\Tests;

/**
 * This class contains the Base tests of the GCMPayload class.
 *
 * @covers Lunr\Vortex\Email\EmailPayload
 */
class EmailPayloadBaseTest extends EmailPayloadTest
{

    /**
     * Test elements is initialized as an empty array.
     */
    public function testElementsIsInitializedAsEmptyArray(): void
    {
        $expected = [
            'charset'  => 'UTF-8',
            'encoding' => 'base64',
        ];

        $this->assertEquals($expected, $this->get_reflection_property_value('elements'));
    }

}

?>
