<?php 

/**
 * This code was generated by
 * \ / _    _  _|   _  _
 * | (_)\/(_)(_|\/| |(/_  v1.0.0
 * /       /
 */

namespace Twilio\Rest;

use Twilio\Domain;
use Twilio\Exceptions\TwilioException;
use Twilio\Rest\Pricing\V1;

/**
 * @property \Twilio\Rest\Pricing\V1 v1
 * @property \Twilio\Rest\Pricing\V1\MessagingList messaging
 * @property \Twilio\Rest\Pricing\V1\PhoneNumberList phoneNumbers
 * @property \Twilio\Rest\Pricing\V1\VoiceList voice
 */
class Pricing extends Domain {
    protected $_v1 = null;

    /**
     * Construct the Pricing Domain
     * 
     * @param \Twilio\Rest\Client $client Twilio\Rest\Client to communicate with
     *                                    Twilio
     * @return \Twilio\Rest\Pricing Domain for Pricing
     */
    public function __construct(Client $client) {
        parent::__construct($client);

        $this->baseUrl = 'https://pricing.twilio.com';
    }

    /**
     * @return \Twilio\Rest\Pricing\V1 Version v1 of pricing
     */
    protected function getV1() {
        if (!$this->_v1) {
            $this->_v1 = new V1($this);
        }
        return $this->_v1;
    }

    /**
     * Magic getter to lazy load version
     * 
     * @param string $name Version to return
     * @return \Twilio\Version The requested version
     * @throws \Twilio\Exceptions\TwilioException For unknown versions
     */
    public function __get($name) {
        $method = 'get' . ucfirst($name);
        if (method_exists($this, $method)) {
            return $this->$method();
        }

        throw new TwilioException('Unknown version ' . $name);
    }

    /**
     * Magic caller to get resource contexts
     * 
     * @param string $name Resource to return
     * @param array $arguments Context parameters
     * @return \Twilio\InstanceContext The requested resource context
     * @throws \Twilio\Exceptions\TwilioException For unknown resource
     */
    public function __call($name, $arguments) {
        $method = 'context' . ucfirst($name);
        if (method_exists($this, $method)) {
            return call_user_func_array(array($this, $method), $arguments);
        }

        throw new TwilioException('Unknown context ' . $name);
    }

    /**
     * @return \Twilio\Rest\Pricing\V1\MessagingList 
     */
    protected function getMessaging() {
        return $this->v1->messaging;
    }

    /**
     * @return \Twilio\Rest\Pricing\V1\PhoneNumberList 
     */
    protected function getPhoneNumbers() {
        return $this->v1->phoneNumbers;
    }

    /**
     * @return \Twilio\Rest\Pricing\V1\VoiceList 
     */
    protected function getVoice() {
        return $this->v1->voice;
    }

    /**
     * Provide a friendly representation
     * 
     * @return string Machine friendly representation
     */
    public function __toString() {
        return '[Twilio.Pricing]';
    }
}