<?php

/**
 * This file contains functionality to generate Windows Phone Tile Push Notification payloads.
 *
 * @package    Lunr\Vortex\MPNS
 * @author     Heinz Wiesinger <heinz@m2mobi.com>
 * @copyright  2013-2018, M2Mobi BV, Amsterdam, The Netherlands
 * @license    http://lunr.nl/LICENSE MIT License
 */

namespace Lunr\Vortex\MPNS;

/**
 * Windows Phone Tile Push Notification Payload Generator.
 */
class MPNSTilePayload extends MPNSPayload
{

    /**
     * Constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Destructor.
     */
    public function __destruct()
    {
        parent::__destruct();
    }

    /**
     * Construct the payload for the push notification.
     *
     * @return string Payload
     */
    public function get_payload(): string
    {
        $xml  = "<?xml version=\"1.0\" encoding=\"utf-8\"?>\n";
        $xml .= "<wp:Notification xmlns:wp=\"WPNotification\">\n";

        if (isset($this->elements['id']) === TRUE)
        {
            $xml .= '<wp:Tile Id="' . $this->elements['id'] . "\">\n";
        }
        else
        {
            $xml .= "<wp:Tile>\n";
        }

        if (isset($this->elements['background_image']) === TRUE)
        {
            $xml .= '<wp:BackgroundImage>' . $this->elements['background_image'] . "</wp:BackgroundImage>\n";
        }

        if (isset($this->elements['count']) === TRUE)
        {
            $xml .= '<wp:Count>' . $this->elements['count'] . "</wp:Count>\n";
        }

        if (isset($this->elements['title']) === TRUE)
        {
            $xml .= '<wp:Title>' . $this->elements['title'] . "</wp:Title>\n";
        }

        if (isset($this->elements['back_background_image']) === TRUE)
        {
            $xml .= '<wp:BackBackgroundImage>' . $this->elements['back_background_image'] . "</wp:BackBackgroundImage>\n";
        }

        if (isset($this->elements['back_title']) === TRUE)
        {
            $xml .= '<wp:BackTitle>' . $this->elements['back_title'] . "</wp:BackTitle>\n";
        }

        if (isset($this->elements['back_content']) === TRUE)
        {
            $xml .= '<wp:BackContent>' . $this->elements['back_content'] . "</wp:BackContent>\n";
        }

        $xml .= "</wp:Tile></wp:Notification>\n";

        return $xml;
    }

    /**
     * Set title for the tile notification.
     *
     * @param string $title Title
     *
     * @return MPNSTilePayload Self Reference
     */
    public function set_title(string $title): self
    {
        $this->elements['title'] = $this->escape_string($title);

        return $this;
    }

    /**
     * Set background image for the tile notification.
     *
     * @param string $image Background Image
     *
     * @return MPNSTilePayload Self Reference
     */
    public function set_background_image(string $image): self
    {
        $this->elements['background_image'] = $this->escape_string($image);

        return $this;
    }

    /**
     * Set count for the tile notification.
     *
     * @param string $count Count
     *
     * @return MPNSTilePayload Self Reference
     */
    public function set_count(string $count): self
    {
        $this->elements['count'] = $this->escape_string($count);

        return $this;
    }

    /**
     * Set back background image for the tile notification.
     *
     * @param string $image Back Background Image
     *
     * @return MPNSTilePayload Self Reference
     */
    public function set_back_background_image(string $image): self
    {
        $this->elements['back_background_image'] = $this->escape_string($image);

        return $this;
    }

    /**
     * Set back title for the tile notification.
     *
     * @param string $title Back Title
     *
     * @return MPNSTilePayload Self Reference
     */
    public function set_back_title(string $title): self
    {
        $this->elements['back_title'] = $this->escape_string($title);

        return $this;
    }

    /**
     * Set back content for the tile notification.
     *
     * @param string $content Back Content
     *
     * @return MPNSTilePayload Self Reference
     */
    public function set_back_content(string $content): self
    {
        $this->elements['back_content'] = $this->escape_string($content);

        return $this;
    }

    /**
     * Set tile ID for the tile notification.
     *
     * @param string $id Tile ID
     *
     * @return MPNSTilePayload Self Reference
     */
    public function set_id(string $id): self
    {
        $this->elements['id'] = $this->escape_string($id);

        return $this;
    }

}

?>
