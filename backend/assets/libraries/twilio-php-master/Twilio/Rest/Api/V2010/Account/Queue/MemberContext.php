<?php 

/**
 * This code was generated by
 * \ / _    _  _|   _  _
 * | (_)\/(_)(_|\/| |(/_  v1.0.0
 * /       /
 */

namespace Twilio\Rest\Api\V2010\Account\Queue;

use Twilio\InstanceContext;
use Twilio\Values;
use Twilio\Version;

class MemberContext extends InstanceContext {
    /**
     * Initialize the MemberContext
     * 
     * @param \Twilio\Version $version Version that contains the resource
     * @param string $accountSid The account_sid
     * @param string $queueSid The Queue in which to find the members
     * @param string $callSid The call_sid
     * @return \Twilio\Rest\Api\V2010\Account\Queue\MemberContext 
     */
    public function __construct(Version $version, $accountSid, $queueSid, $callSid) {
        parent::__construct($version);

        // Path Solution
        $this->solution = array(
            'accountSid' => $accountSid,
            'queueSid' => $queueSid,
            'callSid' => $callSid,
        );

        $this->uri = '/Accounts/' . rawurlencode($accountSid) . '/Queues/' . rawurlencode($queueSid) . '/Members/' . rawurlencode($callSid) . '.json';
    }

    /**
     * Fetch a MemberInstance
     * 
     * @return MemberInstance Fetched MemberInstance
     */
    public function fetch() {
        $params = Values::of(array());

        $payload = $this->version->fetch(
            'GET',
            $this->uri,
            $params
        );

        return new MemberInstance(
            $this->version,
            $payload,
            $this->solution['accountSid'],
            $this->solution['queueSid'],
            $this->solution['callSid']
        );
    }

    /**
     * Update the MemberInstance
     * 
     * @param string $url The url
     * @param string $method The method
     * @return MemberInstance Updated MemberInstance
     */
    public function update($url, $method) {
        $data = Values::of(array(
            'Url' => $url,
            'Method' => $method,
        ));

        $payload = $this->version->update(
            'POST',
            $this->uri,
            array(),
            $data
        );

        return new MemberInstance(
            $this->version,
            $payload,
            $this->solution['accountSid'],
            $this->solution['queueSid'],
            $this->solution['callSid']
        );
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
        return '[Twilio.Api.V2010.MemberContext ' . implode(' ', $context) . ']';
    }
}