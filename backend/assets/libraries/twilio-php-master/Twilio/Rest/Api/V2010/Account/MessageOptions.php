<?php 

/**
 * This code was generated by
 * \ / _    _  _|   _  _
 * | (_)\/(_)(_|\/| |(/_  v1.0.0
 * /       /
 */

namespace Twilio\Rest\Api\V2010\Account;

use Twilio\Options;
use Twilio\Values;

abstract class MessageOptions {
    /**
     * @param string $from The phone number that initiated the message
     * @param string $messagingServiceSid The messaging_service_sid
     * @param string $body The body
     * @param string $mediaUrl The media_url
     * @param string $statusCallback URL Twilio will request when the status changes
     * @param string $applicationSid The application to use for callbacks
     * @param string $maxPrice The max_price
     * @param boolean $provideFeedback The provide_feedback
     * @param integer $validityPeriod The validity_period
     * @param string $maxRate The max_rate
     * @param boolean $forceDelivery The force_delivery
     * @param string $providerSid The provider_sid
     * @param string $contentRetention The content_retention
     * @param string $addressRetention The address_retention
     * @param boolean $smartEncoded The smart_encoded
     * @return CreateMessageOptions Options builder
     */
    public static function create($from = Values::NONE, $messagingServiceSid = Values::NONE, $body = Values::NONE, $mediaUrl = Values::NONE, $statusCallback = Values::NONE, $applicationSid = Values::NONE, $maxPrice = Values::NONE, $provideFeedback = Values::NONE, $validityPeriod = Values::NONE, $maxRate = Values::NONE, $forceDelivery = Values::NONE, $providerSid = Values::NONE, $contentRetention = Values::NONE, $addressRetention = Values::NONE, $smartEncoded = Values::NONE) {
        return new CreateMessageOptions($from, $messagingServiceSid, $body, $mediaUrl, $statusCallback, $applicationSid, $maxPrice, $provideFeedback, $validityPeriod, $maxRate, $forceDelivery, $providerSid, $contentRetention, $addressRetention, $smartEncoded);
    }

    /**
     * @param string $to Filter by messages to this number
     * @param string $from Filter by from number
     * @param string $dateSentBefore Filter by date sent
     * @param string $dateSent Filter by date sent
     * @param string $dateSentAfter Filter by date sent
     * @return ReadMessageOptions Options builder
     */
    public static function read($to = Values::NONE, $from = Values::NONE, $dateSentBefore = Values::NONE, $dateSent = Values::NONE, $dateSentAfter = Values::NONE) {
        return new ReadMessageOptions($to, $from, $dateSentBefore, $dateSent, $dateSentAfter);
    }
}

class CreateMessageOptions extends Options {
    /**
     * @param string $from The phone number that initiated the message
     * @param string $messagingServiceSid The messaging_service_sid
     * @param string $body The body
     * @param string $mediaUrl The media_url
     * @param string $statusCallback URL Twilio will request when the status changes
     * @param string $applicationSid The application to use for callbacks
     * @param string $maxPrice The max_price
     * @param boolean $provideFeedback The provide_feedback
     * @param integer $validityPeriod The validity_period
     * @param string $maxRate The max_rate
     * @param boolean $forceDelivery The force_delivery
     * @param string $providerSid The provider_sid
     * @param string $contentRetention The content_retention
     * @param string $addressRetention The address_retention
     * @param boolean $smartEncoded The smart_encoded
     */
    public function __construct($from = Values::NONE, $messagingServiceSid = Values::NONE, $body = Values::NONE, $mediaUrl = Values::NONE, $statusCallback = Values::NONE, $applicationSid = Values::NONE, $maxPrice = Values::NONE, $provideFeedback = Values::NONE, $validityPeriod = Values::NONE, $maxRate = Values::NONE, $forceDelivery = Values::NONE, $providerSid = Values::NONE, $contentRetention = Values::NONE, $addressRetention = Values::NONE, $smartEncoded = Values::NONE) {
        $this->options['from'] = $from;
        $this->options['messagingServiceSid'] = $messagingServiceSid;
        $this->options['body'] = $body;
        $this->options['mediaUrl'] = $mediaUrl;
        $this->options['statusCallback'] = $statusCallback;
        $this->options['applicationSid'] = $applicationSid;
        $this->options['maxPrice'] = $maxPrice;
        $this->options['provideFeedback'] = $provideFeedback;
        $this->options['validityPeriod'] = $validityPeriod;
        $this->options['maxRate'] = $maxRate;
        $this->options['forceDelivery'] = $forceDelivery;
        $this->options['providerSid'] = $providerSid;
        $this->options['contentRetention'] = $contentRetention;
        $this->options['addressRetention'] = $addressRetention;
        $this->options['smartEncoded'] = $smartEncoded;
    }

    /**
     * A Twilio phone number or alphanumeric sender ID enabled for the type of message you wish to send.
     * 
     * @param string $from The phone number that initiated the message
     * @return $this Fluent Builder
     */
    public function setFrom($from) {
        $this->options['from'] = $from;
        return $this;
    }

    /**
     * The messaging_service_sid
     * 
     * @param string $messagingServiceSid The messaging_service_sid
     * @return $this Fluent Builder
     */
    public function setMessagingServiceSid($messagingServiceSid) {
        $this->options['messagingServiceSid'] = $messagingServiceSid;
        return $this;
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
     * The media_url
     * 
     * @param string $mediaUrl The media_url
     * @return $this Fluent Builder
     */
    public function setMediaUrl($mediaUrl) {
        $this->options['mediaUrl'] = $mediaUrl;
        return $this;
    }

    /**
     * The URL that Twilio will POST to each time your message status changes
     * 
     * @param string $statusCallback URL Twilio will request when the status changes
     * @return $this Fluent Builder
     */
    public function setStatusCallback($statusCallback) {
        $this->options['statusCallback'] = $statusCallback;
        return $this;
    }

    /**
     * Twilio the POST MessageSid as well as MessageStatus to the URL in the MessageStatusCallback property of this Application
     * 
     * @param string $applicationSid The application to use for callbacks
     * @return $this Fluent Builder
     */
    public function setApplicationSid($applicationSid) {
        $this->options['applicationSid'] = $applicationSid;
        return $this;
    }

    /**
     * The max_price
     * 
     * @param string $maxPrice The max_price
     * @return $this Fluent Builder
     */
    public function setMaxPrice($maxPrice) {
        $this->options['maxPrice'] = $maxPrice;
        return $this;
    }

    /**
     * The provide_feedback
     * 
     * @param boolean $provideFeedback The provide_feedback
     * @return $this Fluent Builder
     */
    public function setProvideFeedback($provideFeedback) {
        $this->options['provideFeedback'] = $provideFeedback;
        return $this;
    }

    /**
     * The validity_period
     * 
     * @param integer $validityPeriod The validity_period
     * @return $this Fluent Builder
     */
    public function setValidityPeriod($validityPeriod) {
        $this->options['validityPeriod'] = $validityPeriod;
        return $this;
    }

    /**
     * The max_rate
     * 
     * @param string $maxRate The max_rate
     * @return $this Fluent Builder
     */
    public function setMaxRate($maxRate) {
        $this->options['maxRate'] = $maxRate;
        return $this;
    }

    /**
     * The force_delivery
     * 
     * @param boolean $forceDelivery The force_delivery
     * @return $this Fluent Builder
     */
    public function setForceDelivery($forceDelivery) {
        $this->options['forceDelivery'] = $forceDelivery;
        return $this;
    }

    /**
     * The provider_sid
     * 
     * @param string $providerSid The provider_sid
     * @return $this Fluent Builder
     */
    public function setProviderSid($providerSid) {
        $this->options['providerSid'] = $providerSid;
        return $this;
    }

    /**
     * The content_retention
     * 
     * @param string $contentRetention The content_retention
     * @return $this Fluent Builder
     */
    public function setContentRetention($contentRetention) {
        $this->options['contentRetention'] = $contentRetention;
        return $this;
    }

    /**
     * The address_retention
     * 
     * @param string $addressRetention The address_retention
     * @return $this Fluent Builder
     */
    public function setAddressRetention($addressRetention) {
        $this->options['addressRetention'] = $addressRetention;
        return $this;
    }

    /**
     * The smart_encoded
     * 
     * @param boolean $smartEncoded The smart_encoded
     * @return $this Fluent Builder
     */
    public function setSmartEncoded($smartEncoded) {
        $this->options['smartEncoded'] = $smartEncoded;
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
        return '[Twilio.Api.V2010.CreateMessageOptions ' . implode(' ', $options) . ']';
    }
}

class ReadMessageOptions extends Options {
    /**
     * @param string $to Filter by messages to this number
     * @param string $from Filter by from number
     * @param string $dateSentBefore Filter by date sent
     * @param string $dateSent Filter by date sent
     * @param string $dateSentAfter Filter by date sent
     */
    public function __construct($to = Values::NONE, $from = Values::NONE, $dateSentBefore = Values::NONE, $dateSent = Values::NONE, $dateSentAfter = Values::NONE) {
        $this->options['to'] = $to;
        $this->options['from'] = $from;
        $this->options['dateSentBefore'] = $dateSentBefore;
        $this->options['dateSent'] = $dateSent;
        $this->options['dateSentAfter'] = $dateSentAfter;
    }

    /**
     * Filter by messages to this number
     * 
     * @param string $to Filter by messages to this number
     * @return $this Fluent Builder
     */
    public function setTo($to) {
        $this->options['to'] = $to;
        return $this;
    }

    /**
     * Only show messages from this phone number
     * 
     * @param string $from Filter by from number
     * @return $this Fluent Builder
     */
    public function setFrom($from) {
        $this->options['from'] = $from;
        return $this;
    }

    /**
     * Filter messages sent by this date
     * 
     * @param string $dateSentBefore Filter by date sent
     * @return $this Fluent Builder
     */
    public function setDateSentBefore($dateSentBefore) {
        $this->options['dateSentBefore'] = $dateSentBefore;
        return $this;
    }

    /**
     * Filter messages sent by this date
     * 
     * @param string $dateSent Filter by date sent
     * @return $this Fluent Builder
     */
    public function setDateSent($dateSent) {
        $this->options['dateSent'] = $dateSent;
        return $this;
    }

    /**
     * Filter messages sent by this date
     * 
     * @param string $dateSentAfter Filter by date sent
     * @return $this Fluent Builder
     */
    public function setDateSentAfter($dateSentAfter) {
        $this->options['dateSentAfter'] = $dateSentAfter;
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
        return '[Twilio.Api.V2010.ReadMessageOptions ' . implode(' ', $options) . ']';
    }
}