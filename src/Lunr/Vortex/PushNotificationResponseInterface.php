<?php

/**
 * This file contains the PushNotificationResponseInterface.
 *
 * @package   Lunr\Vortex
 * @author    Damien Tardy-Panis <damien@m2mobi.com>
 * @copyright 2013-2018, M2Mobi BV, Amsterdam, The Netherlands
 * @license   http://lunr.nl/LICENSE MIT License
 */

namespace Lunr\Vortex;

/**
 * Push notification Response interface.
 */
interface PushNotificationResponseInterface
{

    /**
     * Get notification delivery status for an endpoint.
     *
     * @param string $endpoint Endpoint
     *
     * @return PushNotificationStatus::* Delivery status for the endpoint
     */
    public function get_status(string $endpoint): int;

}

?>
