<?php 

/**
 * This code was generated by
 * \ / _    _  _|   _  _
 * | (_)\/(_)(_|\/| |(/_  v1.0.0
 * /       /
 */

namespace Twilio\Rest\Api\V2010\Account\Usage;

use Twilio\InstanceContext;
use Twilio\Options;
use Twilio\Values;
use Twilio\Version;

class TriggerContext extends InstanceContext {
    /**
     * Initialize the TriggerContext
     * 
     * @param \Twilio\Version $version Version that contains the resource
     * @param string $accountSid The account_sid
     * @param string $sid Fetch by unique usage-trigger Sid
     * @return \Twilio\Rest\Api\V2010\Account\Usage\TriggerContext 
     */
    public function __construct(Version $version, $accountSid, $sid) {
        parent::__construct($version);

        // Path Solution
        $this->solution = array(
            'accountSid' => $accountSid,
            'sid' => $sid,
        );

        $this->uri = '/Accounts/' . rawurlencode($accountSid) . '/Usage/Triggers/' . rawurlencode($sid) . '.json';
    }

    /**
     * Fetch a TriggerInstance
     * 
     * @return TriggerInstance Fetched TriggerInstance
     */
    public function fetch() {
        $params = Values::of(array());

        $payload = $this->version->fetch(
            'GET',
            $this->uri,
            $params
        );

        return new TriggerInstance(
            $this->version,
            $payload,
            $this->solution['accountSid'],
            $this->solution['sid']
        );
    }

    /**
     * Update the TriggerInstance
     * 
     * @param array|Options $options Optional Arguments
     * @return TriggerInstance Updated TriggerInstance
     */
    public function update($options = array()) {
        $options = new Values($options);

        $data = Values::of(array(
            'CallbackMethod' => $options['callbackMethod'],
            'CallbackUrl' => $options['callbackUrl'],
            'FriendlyName' => $options['friendlyName'],
        ));

        $payload = $this->version->update(
            'POST',
            $this->uri,
            array(),
            $data
        );

        return new TriggerInstance(
            $this->version,
            $payload,
            $this->solution['accountSid'],
            $this->solution['sid']
        );
    }

    /**
     * Deletes the TriggerInstance
     * 
     * @return boolean True if delete succeeds, false otherwise
     */
    public function delete() {
        return $this->version->delete('delete', $this->uri);
    }

    /**
     * Provide a friendly representation
     * 
     * @return string Machine friendly representation
     */
    public function __toString() {
        $context = array();
        foreach ($this->solution as $key => $value) {
            $context[] = "$key=$value";
        }
        return '[Twilio.Api.V2010.TriggerContext ' . implode(' ', $context) . ']';
    }
}