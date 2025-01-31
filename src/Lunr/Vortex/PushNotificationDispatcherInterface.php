<?php

/**
 * This file contains the PushNotificationDispatcherInterface interface which
 * is the base of all push notifications.
 *
 * @package    Lunr\Vortex
 * @author     Heinz Wiesinger <heinz@m2mobi.com>
 * @author     Olivier Wizen <olivier@m2mobi.com>
 * @copyright  2013-2018, M2Mobi BV, Amsterdam, The Netherlands
 * @license    http://lunr.nl/LICENSE MIT License
 */

namespace Lunr\Vortex;

/**
 * Push notification interface.
 */
interface PushNotificationDispatcherInterface
{

    /**
     * Push the notification.
     *
     * @param object $payload   Payload object
     * @param array  $endpoints Endpoints to sent it to in this batch
     *
     * @return PushNotificationResponseInterface Response object
     */
    public function push(object $payload, array &$endpoints): PushNotificationResponseInterface;

}

?>
