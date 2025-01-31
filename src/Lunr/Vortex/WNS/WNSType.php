<?php

/**
 * This file contains notification types for Windows Push Notifications.
 *
 * @package    Lunr\Vortex\WNS
 * @author     Sean Molenaar <sean@m2mobi.com>
 * @copyright  2013-2018, M2Mobi BV, Amsterdam, The Netherlands
 * @license    http://lunr.nl/LICENSE MIT License
 */

namespace Lunr\Vortex\WNS;

/**
 * Windows Push Notification Types.
 */
class WNSType
{

    /**
     * Tile notification.
     * @var string
     */
    public const TILE = 'tile';

    /**
     * Toast notification.
     * @var string
     */
    public const TOAST = 'toast';

    /**
     * Badge notification.
     * @var string
     */
    public const BADGE = 'badge';

    /**
     * Raw notification.
     * @var string
     */
    public const RAW = 'raw';

}

?>
