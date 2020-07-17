<?php 

/**
 * This code was generated by
 * \ / _    _  _|   _  _
 * | (_)\/(_)(_|\/| |(/_  v1.0.0
 * /       /
 */

namespace Twilio\Tests\Integration\Api\V2010\Account;

use Twilio\Exceptions\DeserializeException;
use Twilio\Exceptions\TwilioException;
use Twilio\Http\Response;
use Twilio\Tests\HolodeckTestCase;
use Twilio\Tests\Request;

class SigningKeyTest extends HolodeckTestCase {
    public function testFetchRequest() {
        $this->holodeck->mock(new Response(500, ''));

        try {
            $this->twilio->api->v2010->accounts("ACaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa")
                                     ->signingKeys("SKaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa")->fetch();
        } catch (DeserializeException $e) {}
          catch (TwilioException $e) {}

        $this->assertRequest(new Request(
            'get',
            'https://api.twilio.com/2010-04-01/Accounts/ACaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa/SigningKeys/SKaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa.json'
        ));
    }

    public function testFetchResponse() {
        $this->holodeck->mock(new Response(
            200,
            '
            {
                "sid": "SKaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa",
                "friendly_name": "foo",
                "date_created": "Mon, 13 Jun 2016 22:50:08 +0000",
                "date_updated": "Mon, 13 Jun 2016 22:50:08 +0000"
            }
            '
        ));

        $actual = $this->twilio->api->v2010->accounts("ACaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa")
                                           ->signingKeys("SKaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa")->fetch();

        $this->assertNotNull($actual);
    }

    public function testUpdateRequest() {
        $this->holodeck->mock(new Response(500, ''));

        try {
            $this->twilio->api->v2010->accounts("ACaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa")
                                     ->signingKeys("SKaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa")->update();
        } catch (DeserializeException $e) {}
          catch (TwilioException $e) {}

        $this->assertRequest(new Request(
            'post',
            'https://api.twilio.com/2010-04-01/Accounts/ACaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa/SigningKeys/SKaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa.json'
        ));
    }

    public function testUpdateResponse() {
        $this->holodeck->mock(new Response(
            200,
            '
            {
                "sid": "SKaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa",
                "friendly_name": "foo",
                "date_created": "Mon, 13 Jun 2016 22:50:08 +0000",
                "date_updated": "Mon, 13 Jun 2016 22:50:08 +0000"
            }
            '
        ));

        $actual = $this->twilio->api->v2010->accounts("ACaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa")
                                           ->signingKeys("SKaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa")->update();

        $this->assertNotNull($actual);
    }

    public function testDeleteRequest() {
        $this->holodeck->mock(new Response(500, ''));

        try {
            $this->twilio->api->v2010->accounts("ACaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa")
                                     ->signingKeys("SKaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa")->delete();
        } catch (DeserializeException $e) {}
          catch (TwilioException $e) {}

        $this->assertRequest(new Request(
            'delete',
            'https://api.twilio.com/2010-04-01/Accounts/ACaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa/SigningKeys/SKaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa.json'
        ));
    }

    public function testDeleteResponse() {
        $this->holodeck->mock(new Response(
            204,
            null
        ));

        $actual = $this->twilio->api->v2010->accounts("ACaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa")
                                           ->signingKeys("SKaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa")->delete();

        $this->assertTrue($actual);
    }

    public function testReadRequest() {
        $this->holodeck->mock(new Response(500, ''));

        try {
            $this->twilio->api->v2010->accounts("ACaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa")
                                     ->signingKeys->read();
        } catch (DeserializeException $e) {}
          catch (TwilioException $e) {}

        $this->assertRequest(new Request(
            'get',
            'https://api.twilio.com/2010-04-01/Accounts/ACaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa/SigningKeys.json'
        ));
    }

    public function testReadFullResponse() {
        $this->holodeck->mock(new Response(
            200,
            '
            {
                "signing_keys": [
                    {
                        "sid": "SKaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa",
                        "friendly_name": "foo",
                        "date_created": "Mon, 13 Jun 2016 22:50:08 +0000",
                        "date_updated": "Mon, 13 Jun 2016 22:50:08 +0000"
                    }
                ],
                "first_page_uri": "/2010-04-01/Accounts/ACaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa/SigningKeys.json?PageSize=50&Page=0",
                "end": 0,
                "previous_page_uri": null,
                "uri": "/2010-04-01/Accounts/ACaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa/SigningKeys.json?PageSize=50&Page=0",
                "page_size": 50,
                "start": 0,
                "next_page_uri": null,
                "page": 0
            }
            '
        ));

        $actual = $this->twilio->api->v2010->accounts("ACaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa")
                                           ->signingKeys->read();

        $this->assertGreaterThan(0, count($actual));
    }

    public function testReadEmptyResponse() {
        $this->holodeck->mock(new Response(
            200,
            '
            {
                "signing_keys": [],
                "first_page_uri": "/2010-04-01/Accounts/ACaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa/SigningKeys.json?PageSize=50&Page=0",
                "end": 0,
                "previous_page_uri": null,
                "uri": "/2010-04-01/Accounts/ACaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa/SigningKeys.json?PageSize=50&Page=0",
                "page_size": 50,
                "start": 0,
                "next_page_uri": null,
                "page": 0
            }
            '
        ));

        $actual = $this->twilio->api->v2010->accounts("ACaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa")
                                           ->signingKeys->read();

        $this->assertNotNull($actual);
    }
}