<?php

/**
 * This file contains functionality to generate Windows Toast Push Notification payloads.
 *
 * @package    Lunr\Vortex\WNS
 * @author     Sean Molenaar <sean@m2mobi.com>
 * @copyright  2013-2018, M2Mobi BV, Amsterdam, The Netherlands
 * @license    http://lunr.nl/LICENSE MIT License
 */

namespace Lunr\Vortex\WNS;

/**
 * Windows Toast Push Notification Payload Generator.
 */
class WNSToastPayload extends WNSPayload
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
        $template = (isset($this->elements['template'])) ? $this->elements['template'] : 'ToastText0' . count($this->elements['text']);

        $launch = '';
        if (isset($this->elements['launch']))
        {
            $launch = 'launch="' . $this->elements['launch'] . '"';
        }

        $xml  = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
        $xml .= '<toast ' . $launch . ">\n";

        $xml .= "<visual>\n";
        $xml .= '<binding template="' . $template . "\">\n";

        if (isset($this->elements['image']))
        {
            $xml .= '<image id="1" src="' . $this->elements['image'] . "\"/>\n";
        }

        foreach ($this->elements['text'] as $key => $value)
        {
            $xml .= '<text id="' . ( $key + 1 ) . '">' . $value . "</text>\n";
        }

        $xml .= "</binding>\n";
        $xml .= "</visual>\n";
        $xml .= "</toast>\n";

        return $xml;
    }

    /**
     * Set text for the toast notification.
     *
     * @param string[]|string $text Message
     *
     * @param int             $line The line on which to add the text
     *
     * @return self Self Reference
     */
    public function set_text($text, int $line = 0): self
    {
        if (!is_array($text))
        {
            $this->elements['text'][$line] = $this->escape_string($text);
            return $this;
        }

        foreach ($text as $key => $value)
        {
            $this->elements['text'][$key] = $this->escape_string($value);
        }

        return $this;
    }

    /**
     * Set launch parameter for the toast notification.
     *
     * @param string $launch Launch parameters for the app
     *
     * @return self Self Reference
     */
    public function set_launch(string $launch): self
    {
        $this->elements['launch'] = $this->escape_string($launch);

        return $this;
    }

    /**
     * Set template for the toast notification.
     *
     * @param string $template Template for the notification
     *
     * @see https://msdn.microsoft.com/en-us/library/windows/apps/windows.ui.notifications.toasttemplatetype
     *
     * @return self Self Reference
     */
    public function set_template(string $template): self
    {
        $this->elements['template'] = $this->escape_string($template);

        return $this;
    }

    /**
     * Set image for the toast notification.
     *
     * @param string $image Image to display
     *
     * @return self Self Reference
     */
    public function set_image(string $image): self
    {
        $this->elements['image'] = $this->escape_string($image);

        return $this;
    }

}

?>
