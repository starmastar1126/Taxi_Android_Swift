<?php 

/**
 * This code was generated by
 * \ / _    _  _|   _  _
 * | (_)\/(_)(_|\/| |(/_  v1.0.0
 * /       /
 */

namespace Twilio\Rest\Chat\V1\Service\Channel;

use Twilio\Options;
use Twilio\Values;

abstract class MessageOptions {
    /**
     * @param string $from The from
     * @param string $attributes The attributes
     * @return CreateMessageOptions Options builder
     */
    public static function create($from = Values::NONE, $attributes = Values::NONE) {
        return new CreateMessageOptions($from, $attributes);
    }

    /**
     * @param string $order The order
     * @return ReadMessageOptions Options builder
     */
    public static function read($order = Values::NONE) {
        return new ReadMessageOptions($order);
    }

    /**
     * @param string $body The body
     * @param string $attributes The attributes
     * @return UpdateMessageOptions Options builder
     */
    public static function update($body = Values::NONE, $attributes = Values::NONE) {
        return new UpdateMessageOptions($body, $attributes);
    }
}

class CreateMessageOptions extends Options {
    /**
     * @param string $from The from
     * @param string $attributes The attributes
     */
    public function __construct($from = Values::NONE, $attributes = Values::NONE) {
        $this->options['from'] = $from;
        $this->options['attributes'] = $attributes;
    }

    /**
     * The from
     * 
     * @param string $from The from
     * @return $this Fluent Builder
     */
    public function setFrom($from) {
        $this->options['from'] = $from;
        return $this;
    }

    /**
     * The attributes
     * 
     * @param string $attributes The attributes
     * @return $this Fluent Builder
     */
    public function setAttributes($attributes) {
        $this->options['attributes'] = $attributes;
        return $this;
    }

    /**
     * Provide a friendly representation
     * 
     * @return string Machine friendly representation
     */
    public function __toString() {
        $options = array();
        foreach ($this->options as $key => $value) {
            if ($value != Values::NONE) {
                $options[] = "$key=$value";
            }
        }
        return '[Twilio.Chat.V1.CreateMessageOptions ' . implode(' ', $options) . ']';
    }
}

class ReadMessageOptions extends Options {
    /**
     * @param string $order The order
     */
    public function __construct($order = Values::NONE) {
        $this->options['order'] = $order;
    }

    /**
     * The order
     * 
     * @param string $order The order
     * @return $this Fluent Builder
     */
    public function setOrder($order) {
        $this->options['order'] = $order;
        return $this;
    }

    /**
     * Provide a friendly representation
     * 
     * @return string Machine friendly representation
     */
    public function __toString() {
        $options = array();
        foreach ($this->options as $key => $value) {
            if ($value != Values::NONE) {
                $options[] = "$key=$value";
            }
        }
        return '[Twilio.Chat.V1.ReadMessageOptions ' . implode(' ', $options) . ']';
    }
}

class UpdateMessageOptions extends Options {
    /**
     * @param string $body The body
     * @param string $attributes The attributes
     */
    public function __construct($body = Values::NONE, $attributes = Values::NONE) {
        $this->options['body'] = $body;
        $this->options['attributes'] = $attributes;
    }

    /**
     * The body
     * 
     * @param string $body The body
     * @return $this Fluent Builder
     */
    public function setBody($body) {
        $this->options['body'] = $body;
        return $this;
    }

    /**
     * The attributes
     * 
     * @param string $attributes The attributes
     * @return $this Fluent Builder
     */
    public function setAttributes($attributes) {
        $this->options['attributes'] = $attributes;
        return $this;
    }

    /**
     * Provide a friendly representation
     * 
     * @return string Machine friendly representation
     */
    public function __toString() {
        $options = array();
        foreach ($this->options as $key => $value) {
            if ($value != Values::NONE) {
                $options[] = "$key=$value";
            }
        }
        return '[Twilio.Chat.V1.UpdateMessageOptions ' . implode(' ', $options) . ']';
    }
}