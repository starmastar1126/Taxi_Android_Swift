<?php 

class Services_Twilio_Rest_Address
    extends Services_Twilio_InstanceResource
{
    protected function init($client, $uri)
    {
        $this->setupSubresources(
            'dependent_phone_numbers'
        );
    }

    /**
     * Make a request to delete the specified resource.
     *
     * :rtype: boolean
     */
    public function delete()
    {
        return $this->client->deleteData($this->uri);
    }
}
