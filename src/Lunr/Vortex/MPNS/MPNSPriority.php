<?php

/**
 * This file contains priority types for delivering Windows Phone Push Notifications.
 *
 * @package    Lunr\Vortex\MPNS
 * @author     Heinz Wiesinger <heinz@m2mobi.com>
 * @copyright  2013-2018, M2Mobi BV, Amsterdam, The Netherlands
 * @license    http://lunr.nl/LICENSE MIT License
 */

namespace Lunr\Vortex\MPNS;

/**
 * Windows Phone Push Notification Priority Types.
 */
class MPNSPriority
{

    /**
     * Initial priority.
     * @var integer
     */
    public const DEFAULT       = 0;

    /**
     * Deliver Tile notification immediately.
     * @var integer
     */
    public const TILE_IMMEDIATELY = 1;

    /**
     * Deliver Tile notification within 450 seconds.
     * @var integer
     */
    public const TILE_WAIT_450 = 11;

    /**
     * Deliver Tile notification within 900 seconds.
     * @var integer
     */
    public const TILE_WAIT_900 = 21;

    /**
     * Deliver Toast notification immediately.
     * @var integer
     */
    public const TOAST_IMMEDIATELY = 2;

    /**
     * Deliver Toast notification within 450 seconds.
     * @var integer
     */
    public const TOAST_WAIT_450 = 12;

    /**
     * Deliver Toast notification within 900 seconds.
     * @var integer
     */
    public const TOAST_WAIT_900 = 22;

    /**
     * Deliver Raw notification immediately.
     * @var integer
     */
    public const RAW_IMMEDIATELY = 3;

    /**
     * Deliver Raw notification within 450 seconds.
     * @var integer
     */
    public const RAW_WAIT_450 = 13;

    /**
     * Deliver Raw notification within 900 seconds.
     * @var integer
     */
    public const RAW_WAIT_900 = 23;

}

?>
