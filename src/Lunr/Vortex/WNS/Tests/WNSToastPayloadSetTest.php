<?php

/**
 * This file contains the WNSToastPayloadSetTest class.
 *
 * PHP Version 5.4
 *
 * @package    Lunr\Vortex\WNS
 * @author     Sean Molenaar <sean@m2mobi.com>
 * @copyright  2013-2016, M2Mobi BV, Amsterdam, The Netherlands
 * @license    http://lunr.nl/LICENSE MIT License
 */

namespace Lunr\Vortex\WNS\Tests;

use Lunr\Halo\PsrLoggerTestTrait;

/**
 * This class contains tests for the setters of the WNSToastPayload class.
 *
 * @covers Lunr\Vortex\WNS\WNSToastPayload
 */
class WNSToastPayloadSetTest extends WNSToastPayloadTest
{

    use PsrLoggerTestTrait;

    /**
     * Test set_title() works correctly.
     *
     * @covers Lunr\Vortex\WNS\WNSToastPayload::set_title
     */
    public function testSetTitle()
    {
        $this->class->set_title('&title');

        $value = $this->get_reflection_property_value('elements');

        $this->assertArrayHasKey('title', $value);
        $this->assertEquals('&amp;title', $value['title']);
    }

    /**
     * Test fluid interface of set_title().
     *
     * @covers Lunr\Vortex\WNS\WNSToastPayload::set_title
     */
    public function testSetTitleReturnsSelfReference()
    {
        $this->assertSame($this->class, $this->class->set_title('title'));
    }

    /**
     * Test set_message() works correctly.
     *
     * @covers Lunr\Vortex\WNS\WNSToastPayload::set_message
     */
    public function testSetMessage()
    {
        $this->class->set_message('&message');

        $value = $this->get_reflection_property_value('elements');

        $this->assertArrayHasKey('message', $value);
        $this->assertEquals('&amp;message', $value['message']);
    }

    /**
     * Test fluid interface of set_message().
     *
     * @covers Lunr\Vortex\WNS\WNSToastPayload::set_message
     */
    public function testSetMessageReturnsSelfReference()
    {
        $this->assertSame($this->class, $this->class->set_message('message'));
    }

    /**
     * Test set_deeplink() with correct links.
     *
     * @covers Lunr\Vortex\WNS\WNSToastPayload::set_deeplink
     */
    public function testSetDeeplinkWithCorrectLink()
    {
        $this->class->set_deeplink('/page&link');

        $value = $this->get_reflection_property_value('elements');

        $this->assertArrayHasKey('deeplink', $value);
        $this->assertEquals('/page&amp;link', $value['deeplink']);
    }

    /**
     * Test set_deeplink() with too long links.
     *
     * @covers Lunr\Vortex\WNS\WNSToastPayload::set_deeplink
     */
    public function testSetDeeplinkWithTooLongLink()
    {
        $string = '<&';

        for ($i = 0; $i < 127; $i++)
        {
            $string .= 'aa';
        }

        $this->logger->expects($this->once())
                     ->method('notice')
                     ->with($this->equalTo('Deeplink for Windows Toast Notification too long. Truncated.'));

        $this->class->set_deeplink($string);

        $value = $this->get_reflection_property_value('elements');

        $this->assertArrayHasKey('deeplink', $value);
        $this->assertEquals('&lt;&amp;' . substr($string, 2, 247), $value['deeplink']);
        $this->assertEquals(256, strlen($value['deeplink']));
    }

    /**
     * Test fluid interface of set_deeplink().
     *
     * @covers Lunr\Vortex\WNS\WNSToastPayload::set_deeplink
     */
    public function testSetDeeplinkReturnsSelfReference()
    {
        $this->assertSame($this->class, $this->class->set_deeplink('link'));
    }

}

?>
