<?php 

/**
 * This code was generated by
 * \ / _    _  _|   _  _
 * | (_)\/(_)(_|\/| |(/_  v1.0.0
 * /       /
 */

namespace Twilio\Rest\Proxy\V1;

use Twilio\Options;
use Twilio\Values;

/**
 * PLEASE NOTE that this class contains beta products that are subject to change. Use them with caution.
 */
abstract class ServiceOptions {
    /**
     * @param string $friendlyName A human readable description of this resource.
     * @param integer $defaultTtl Default TTL for a Session, in seconds.
     * @param string $callbackUrl URL Twilio will send callbacks to
     * @return CreateServiceOptions Options builder
     */
    public static function create($friendlyName = Values::NONE, $defaultTtl = Values::NONE, $callbackUrl = Values::NONE) {
        return new CreateServiceOptions($friendlyName, $defaultTtl, $callbackUrl);
    }

    /**
     * @param string $friendlyName A human readable description of this resource.
     * @param integer $defaultTtl Default TTL for a Session, in seconds.
     * @param string $callbackUrl URL Twilio will send callbacks to
     * @return UpdateServiceOptions Options builder
     */
    public static function update($friendlyName = Values::NONE, $defaultTtl = Values::NONE, $callbackUrl = Values::NONE) {
        return new UpdateServiceOptions($friendlyName, $defaultTtl, $callbackUrl);
    }
}

class CreateServiceOptions extends Options {
    /**
     * @param string $friendlyName A human readable description of this resource.
     * @param integer $defaultTtl Default TTL for a Session, in seconds.
     * @param string $callbackUrl URL Twilio will send callbacks to
     */
    public function __construct($friendlyName = Values::NONE, $defaultTtl = Values::NONE, $callbackUrl = Values::NONE) {
        $this->options['friendlyName'] = $friendlyName;
        $this->options['defaultTtl'] = $defaultTtl;
        $this->options['callbackUrl'] = $callbackUrl;
    }

    /**
     * A human readable description of this resource, up to 64 characters.
     * 
     * @param string $friendlyName A human readable description of this resource.
     * @return $this Fluent Builder
     */
    public function setFriendlyName($friendlyName) {
        $this->options['friendlyName'] = $friendlyName;
        return $this;
    }

    /**
     * The default Time to Live for a Session, in seconds.
     * 
     * @param integer $defaultTtl Default TTL for a Session, in seconds.
     * @return $this Fluent Builder
     */
    public function setDefaultTtl($defaultTtl) {
        $this->options['defaultTtl'] = $defaultTtl;
        return $this;
    }

    /**
     * The URL Twilio will send callbacks to.
     * 
     * @param string $callbackUrl URL Twilio will send callbacks to
     * @return $this Fluent Builder
     */
    public function setCallbackUrl($callbackUrl) {
        $this->options['callbackUrl'] = $callbackUrl;
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
        return '[Twilio.Proxy.V1.CreateServiceOptions ' . implode(' ', $options) . ']';
    }
}

class UpdateServiceOptions extends Options {
    /**
     * @param string $friendlyName A human readable description of this resource.
     * @param integer $defaultTtl Default TTL for a Session, in seconds.
     * @param string $callbackUrl URL Twilio will send callbacks to
     */
    public function __construct($friendlyName = Values::NONE, $defaultTtl = Values::NONE, $callbackUrl = Values::NONE) {
        $this->options['friendlyName'] = $friendlyName;
        $this->options['defaultTtl'] = $defaultTtl;
        $this->options['callbackUrl'] = $callbackUrl;
    }

    /**
     * A human readable description of this resource, up to 64 characters.
     * 
     * @param string $friendlyName A human readable description of this resource.
     * @return $this Fluent Builder
     */
    public function setFriendlyName($friendlyName) {
        $this->options['friendlyName'] = $friendlyName;
        return $this;
    }

    /**
     * The default Time to Live for a Session, in seconds.
     * 
     * @param integer $defaultTtl Default TTL for a Session, in seconds.
     * @return $this Fluent Builder
     */
    public function setDefaultTtl($defaultTtl) {
        $this->options['defaultTtl'] = $defaultTtl;
        return $this;
    }

    /**
     * The URL Twilio will send callbacks to.
     * 
     * @param string $callbackUrl URL Twilio will send callbacks to
     * @return $this Fluent Builder
     */
    public function setCallbackUrl($callbackUrl) {
        $this->options['callbackUrl'] = $callbackUrl;
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
        return '[Twilio.Proxy.V1.UpdateServiceOptions ' . implode(' ', $options) . ']';
    }
}