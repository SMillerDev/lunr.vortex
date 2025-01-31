<?php

/**
 * This file contains the EmailDispatcherPushTest class.
 *
 * @package    Lunr\Vortex\Email
 * @author     Leonidas Diamantis <leonidas@m2mobi.com>
 * @copyright  2014-2018, M2Mobi BV, Amsterdam, The Netherlands
 * @license    http://lunr.nl/LICENSE MIT License
 */

namespace Lunr\Vortex\Email\Tests;

use PHPMailer\PHPMailer\Exception as PHPMailerException;

/**
 * This class contains test for the push() method of the EmailDispatcher class.
 *
 * @covers Lunr\Vortex\Email\EmailDispatcher
 */
class EmailDispatcherPushTest extends EmailDispatcherTest
{

    /**
     * Test that push() returns EmailResponseObject.
     *
     * @covers Lunr\Vortex\Email\EmailDispatcher::push
     */
    public function testPushReturnsEmailResponseObject(): void
    {
        $endpoints = [ 'recipient@domain.com' ];

        $this->payload->expects($this->once())
                      ->method('get_payload')
                      ->willReturn('{"subject": "subject", "body": "body", "charset": "UTF-8", "encoding": "base64"}');

        $this->set_reflection_property_value('source', 'sender@domain.com');

        $this->mock_method([ $this->class, 'clone_mail' ], function() { return $this->mail_transport; }, 'private');

        $this->mail_transport->expects($this->once())
                             ->method('setFrom')
                             ->with($this->get_reflection_property_value('source'));

        $this->mail_transport->expects($this->once())
                             ->method('addAddress')
                             ->with('recipient@domain.com');

        $this->mail_transport->expects($this->once())
                             ->method('send')
                             ->will($this->returnValue(TRUE));

        $this->mail_transport->expects($this->once())
                             ->method('clearAddresses');

        $this->mail_transport->expects($this->once())
                             ->method('getSentMIMEMessage')
                             ->will($this->returnValue(''));

        $this->assertInstanceOf('Lunr\Vortex\Email\EmailResponse', $this->class->push($this->payload, $endpoints));

        $this->assertEquals($this->mail_transport->Subject, 'subject');
        $this->assertEquals($this->mail_transport->Body, 'body');
        $this->assertEquals($this->mail_transport->CharSet, 'UTF-8');
        $this->assertEquals($this->mail_transport->Encoding, 'base64');

        $this->unmock_method([ $this->class, 'clone_mail' ]);
    }

    /**
     * Test that push() returns EmailResponseObject also on error.
     *
     * @covers Lunr\Vortex\Email\EmailDispatcher::push
     */
    public function testPushReturnsEmailResponseObjectOnError(): void
    {
        $endpoints = [ 'recipient@domain.com' ];

        $this->payload->expects($this->once())
                      ->method('get_payload')
                      ->willReturn('{"subject": "subject", "body": "body", "charset": "UTF-8", "encoding": "base64"}');

        $this->set_reflection_property_value('source', 'sender@domain.com');

        $this->mock_method([ $this->class, 'clone_mail' ], function() { return $this->mail_transport; }, 'private');

        $this->mail_transport->expects($this->once())
                             ->method('setFrom')
                             ->with($this->get_reflection_property_value('source'));

        $this->mail_transport->expects($this->once())
                             ->method('addAddress')
                             ->with('recipient@domain.com');

        $this->mail_transport->expects($this->once())
                             ->method('send')
                             ->will($this->throwException(new PHPMailerException));

        $this->mail_transport->expects($this->once())
                             ->method('clearAddresses');

        $this->mail_transport->expects($this->once())
                             ->method('getSentMIMEMessage')
                             ->will($this->returnValue(''));

        $this->assertInstanceOf('Lunr\Vortex\Email\EmailResponse', $this->class->push($this->payload, $endpoints));

        $this->assertEquals($this->mail_transport->Subject, 'subject');
        $this->assertEquals($this->mail_transport->Body, 'body');
        $this->assertEquals($this->mail_transport->CharSet, 'UTF-8');
        $this->assertEquals($this->mail_transport->Encoding, 'base64');

        $this->unmock_method([ $this->class, 'clone_mail' ]);
    }

    /**
     * Test that push() resets the properties after a push.
     *
     * @covers Lunr\Vortex\Email\EmailDispatcher::push
     */
    public function testPushResetsProperties(): void
    {
        $endpoints = [ 'recipient@domain.com' ];

        $this->payload->expects($this->once())
                      ->method('get_payload')
                      ->willReturn('{"subject": "subject", "body": "body", "charset": "UTF-8", "encoding": "base64"}');

        $this->set_reflection_property_value('source', 'sender@domain.com');

        $this->mock_method([ $this->class, 'clone_mail' ], function() { return $this->mail_transport; }, 'private');

        $this->mail_transport->expects($this->once())
                             ->method('send')
                             ->will($this->returnValue(TRUE));

        $this->mail_transport->expects($this->once())
                             ->method('getSentMIMEMessage')
                             ->will($this->returnValue(''));

        $this->class->push($this->payload, $endpoints);

        $this->unmock_method([ $this->class, 'clone_mail' ]);
    }

}

?>
