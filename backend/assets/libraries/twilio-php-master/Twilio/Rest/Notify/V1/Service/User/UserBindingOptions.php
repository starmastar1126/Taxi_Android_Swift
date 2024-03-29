<?php 

/**
 * This code was generated by
 * \ / _    _  _|   _  _
 * | (_)\/(_)(_|\/| |(/_  v1.0.0
 * /       /
 */

namespace Twilio\Rest\Notify\V1\Service\User;

use Twilio\Options;
use Twilio\Values;

/**
 * PLEASE NOTE that this class contains beta products that are subject to change. Use them with caution.
 */
abstract class UserBindingOptions {
    /**
     * @param string $tag The tag
     * @param string $notificationProtocolVersion The notification_protocol_version
     * @param string $credentialSid The credential_sid
     * @param string $endpoint The endpoint
     * @return CreateUserBindingOptions Options builder
     */
    public static function create($tag = Values::NONE, $notificationProtocolVersion = Values::NONE, $credentialSid = Values::NONE, $endpoint = Values::NONE) {
        return new CreateUserBindingOptions($tag, $notificationProtocolVersion, $credentialSid, $endpoint);
    }

    /**
     * @param \DateTime $startDate The start_date
     * @param \DateTime $endDate The end_date
     * @param string $tag The tag
     * @return ReadUserBindingOptions Options builder
     */
    public static function read($startDate = Values::NONE, $endDate = Values::NONE, $tag = Values::NONE) {
        return new ReadUserBindingOptions($startDate, $endDate, $tag);
    }
}

class CreateUserBindingOptions extends Options {
    /**
     * @param string $tag The tag
     * @param string $notificationProtocolVersion The notification_protocol_version
     * @param string $credentialSid The credential_sid
     * @param string $endpoint The endpoint
     */
    public function __construct($tag = Values::NONE, $notificationProtocolVersion = Values::NONE, $credentialSid = Values::NONE, $endpoint = Values::NONE) {
        $this->options['tag'] = $tag;
        $this->options['notificationProtocolVersion'] = $notificationProtocolVersion;
        $this->options['credentialSid'] = $credentialSid;
        $this->options['endpoint'] = $endpoint;
    }

    /**
     * The tag
     * 
     * @param string $tag The tag
     * @return $this Fluent Builder
     */
    public function setTag($tag) {
        $this->options['tag'] = $tag;
        return $this;
    }

    /**
     * The notification_protocol_version
     * 
     * @param string $notificationProtocolVersion The notification_protocol_version
     * @return $this Fluent Builder
     */
    public function setNotificationProtocolVersion($notificationProtocolVersion) {
        $this->options['notificationProtocolVersion'] = $notificationProtocolVersion;
        return $this;
    }

    /**
     * The credential_sid
     * 
     * @param string $credentialSid The credential_sid
     * @return $this Fluent Builder
     */
    public function setCredentialSid($credentialSid) {
        $this->options['credentialSid'] = $credentialSid;
        return $this;
    }

    /**
     * The endpoint
     * 
     * @param string $endpoint The endpoint
     * @return $this Fluent Builder
     */
    public function setEndpoint($endpoint) {
        $this->options['endpoint'] = $endpoint;
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
        return '[Twilio.Notify.V1.CreateUserBindingOptions ' . implode(' ', $options) . ']';
    }
}

class ReadUserBindingOptions extends Options {
    /**
     * @param \DateTime $startDate The start_date
     * @param \DateTime $endDate The end_date
     * @param string $tag The tag
     */
    public function __construct($startDate = Values::NONE, $endDate = Values::NONE, $tag = Values::NONE) {
        $this->options['startDate'] = $startDate;
        $this->options['endDate'] = $endDate;
        $this->options['tag'] = $tag;
    }

    /**
     * The start_date
     * 
     * @param \DateTime $startDate The start_date
     * @return $this Fluent Builder
     */
    public function setStartDate($startDate) {
        $this->options['startDate'] = $startDate;
        return $this;
    }

    /**
     * The end_date
     * 
     * @param \DateTime $endDate The end_date
     * @return $this Fluent Builder
     */
    public function setEndDate($endDate) {
        $this->options['endDate'] = $endDate;
        return $this;
    }

    /**
     * The tag
     * 
     * @param string $tag The tag
     * @return $this Fluent Builder
     */
    public function setTag($tag) {
        $this->options['tag'] = $tag;
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
        return '[Twilio.Notify.V1.ReadUserBindingOptions ' . implode(' ', $options) . ']';
    }
}