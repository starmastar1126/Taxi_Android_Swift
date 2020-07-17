<?php 

class Services_Twilio_Rest_Trunking_PhoneNumbers extends Services_Twilio_TrunkingListResource {

    /**
     * Create a new PhoneNumber instance
     *
     * Example usage:
     *
     * .. code-block:: php
     *
     *      $trunkingClient->trunks->get('TK123')->phone_numbers->create(array(
     *          "PhoneNumberSid" => "PN1234xxxx"
     *      ));
     *
     * :param array $params: a single array of parameters which is serialized and
     *      sent directly to the Twilio API.
     *
     */
    public function create($params = array()) {
        return parent::_create($params);
    }
}
