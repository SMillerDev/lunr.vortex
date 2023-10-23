<?php

/**
 * This file contains functionality to generate Firebase Cloud Messaging Push Notification payloads.
 *
 * SPDX-FileCopyrightText: Copyright 2017 M2mobi B.V., Amsterdam, The Netherlands
 * SPDX-FileCopyrightText: Copyright 2022 Move Agency Group B.V., Zwolle, The Netherlands
 * SPDX-License-Identifier: MIT
 */

namespace Lunr\Vortex\FCM;

use ReflectionClass;

/**
 * Firebase Cloud Messaging Push Notification Payload Generator.
 */
class FCMPayload
{

    /**
     * Array of Push Notification elements.
     * @var array
     */
    protected array $elements;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->elements = [];

        $this->elements['priority'] = FCMPriority::HIGH;
    }

    /**
     * Destructor.
     */
    public function __destruct()
    {
        unset($this->elements);
    }

    /**
     * Construct the payload for the push notification.
     *
     * @return string FCMPayload
     */
    public function get_payload(): string
    {
        return json_encode($this->elements);
    }

    /**
     * Sets the payload key collapse_key.
     *
     * An arbitrary string that is used to collapse a group of alike messages
     * when the device is offline, so that only the last message gets sent to the client.
     *
     * @param string $key The notification collapse key identifier
     *
     * @return FCMPayload Self Reference
     */
    public function set_collapse_key(string $key): self
    {
        $this->elements['collapse_key'] = $key;

        return $this;
    }

    /**
     * Sets the payload key data.
     *
     * The fields of data represent the key-value pairs of the message's payload data.
     *
     * @param array $data The actual notification information
     *
     * @return FCMPayload Self Reference
     */
    public function set_data(array $data): self
    {
        $this->elements['data'] = $data;

        return $this;
    }

    /**
     * Sets the payload key time_to_live.
     *
     * It defines how long (in seconds) the message should be kept on FCM storage,
     * if the device is offline.
     *
     * @param int $ttl The time in seconds for the notification to stay alive
     *
     * @return FCMPayload Self Reference
     */
    public function set_time_to_live(int $ttl): self
    {
        $this->elements['time_to_live'] = $ttl;

        return $this;
    }

    /**
     * Check whether a condition is set
     *
     * @return bool TRUE if condition is present.
     */
    public function has_condition(): bool
    {
        return isset($this->elements['condition']);
    }

    /**
     * Check whether a condition is set
     *
     * @return bool TRUE if condition is present.
     */
    public function has_topic(): bool
    {
        return isset($this->elements['topic']);
    }

    /**
     * Sets the payload key notification.
     *
     * The fields of data represent the key-value pairs of the message's payload notification data.
     *
     * @param array $notification The actual notification information
     *
     * @return FCMPayload Self Reference
     */
    public function set_notification(array $notification): self
    {
        $this->elements['notification'] = $notification;

        return $this;
    }

    /**
     * Sets the notification as providing content.
     *
     * @param bool $val Value for the "content_available" field.
     *
     * @return FCMPayload Self Reference
     */
    public function set_content_available(bool $val): self
    {
        $this->elements['content_available'] = $val;

        return $this;
    }

    /**
     * Sets the topic name to send the message to.
     *
     * @param string $topic String of the topic name
     *
     * @return FCMPayload Self Reference
     */
    public function set_topic(string $topic): self
    {
        $this->elements['topic'] = $topic;

        return $this;
    }

    /**
     * Sets the condition to send the message to. For example:
     * 'TopicA' in topics && ('TopicB' in topics || 'TopicC' in topics)
     *
     * You can include up to five topics in your conditional expression.
     * Conditions support the following operators: &&, ||, !
     *
     * @param string $condition Key-value pairs of payload data
     *
     * @return FCMPayload Self Reference
     */
    public function set_condition(string $condition): self
    {
        $this->elements['condition'] = $condition;

        return $this;
    }

    /**
     * Mark the notification as mutable.
     *
     * @param bool $mutable Notification mutable_content value.
     *
     * @return FCMPayload Self Reference
     */
    public function set_mutable_content(bool $mutable): self
    {
        $this->elements['mutable_content'] = $mutable;

        return $this;
    }

    /**
     * Mark the notification priority.
     *
     * @param string $priority Notification priority value.
     *
     * @return FCMPayload Self Reference
     */
    public function set_priority(string $priority): self
    {
        $priority       = strtolower($priority);
        $priority_class = new ReflectionClass('\Lunr\Vortex\FCM\FCMPriority');
        if (in_array($priority, array_values($priority_class->getConstants())))
        {
            $this->elements['priority'] = $priority;
        }

        return $this;
    }

    /**
     * Set additional FCM values in the 'fcm_options' key.
     *
     * @param string $key   Options key.
     * @param string $value Options value.
     *
     * @return FCMPayload Self Reference
     */
    public function set_options(string $key, string $value): self
    {
        $this->elements['fcm_options'][$key] = $value;

        return $this;
    }

}

?>
