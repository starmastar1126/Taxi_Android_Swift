<?php 

/**
 * This code was generated by
 * \ / _    _  _|   _  _
 * | (_)\/(_)(_|\/| |(/_  v1.0.0
 * /       /
 */

namespace Twilio\Rest\Notify\V1\Service\User;

use Twilio\InstanceContext;
use Twilio\Values;
use Twilio\Version;

/**
 * PLEASE NOTE that this class contains beta products that are subject to change. Use them with caution.
 */
class SegmentMembershipContext extends InstanceContext {
    /**
     * Initialize the SegmentMembershipContext
     * 
     * @param \Twilio\Version $version Version that contains the resource
     * @param string $serviceSid The service_sid
     * @param string $identity The identity
     * @param string $segment The segment
     * @return \Twilio\Rest\Notify\V1\Service\User\SegmentMembershipContext 
     */
    public function __construct(Version $version, $serviceSid, $identity, $segment) {
        parent::__construct($version);

        // Path Solution
        $this->solution = array(
            'serviceSid' => $serviceSid,
            'identity' => $identity,
            'segment' => $segment,
        );

        $this->uri = '/Services/' . rawurlencode($serviceSid) . '/Users/' . rawurlencode($identity) . '/SegmentMemberships/' . rawurlencode($segment) . '';
    }

    /**
     * Deletes the SegmentMembershipInstance
     * 
     * @return boolean True if delete succeeds, false otherwise
     */
    public function delete() {
        return $this->version->delete('delete', $this->uri);
    }

    /**
     * Fetch a SegmentMembershipInstance
     * 
     * @return SegmentMembershipInstance Fetched SegmentMembershipInstance
     */
    public function fetch() {
        $params = Values::of(array());

        $payload = $this->version->fetch(
            'GET',
            $this->uri,
            $params
        );

        return new SegmentMembershipInstance(
            $this->version,
            $payload,
            $this->solution['serviceSid'],
            $this->solution['identity'],
            $this->solution['segment']
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
        return '[Twilio.Notify.V1.SegmentMembershipContext ' . implode(' ', $context) . ']';
    }
}