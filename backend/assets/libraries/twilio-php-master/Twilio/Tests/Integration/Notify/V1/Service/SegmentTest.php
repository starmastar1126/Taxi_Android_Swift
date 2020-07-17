<?php 

/**
 * This code was generated by
 * \ / _    _  _|   _  _
 * | (_)\/(_)(_|\/| |(/_  v1.0.0
 * /       /
 */

namespace Twilio\Tests\Integration\Notify\V1\Service;

use Twilio\Exceptions\DeserializeException;
use Twilio\Exceptions\TwilioException;
use Twilio\Http\Response;
use Twilio\Tests\HolodeckTestCase;
use Twilio\Tests\Request;

class SegmentTest extends HolodeckTestCase {
    public function testReadRequest() {
        $this->holodeck->mock(new Response(500, ''));

        try {
            $this->twilio->notify->v1->services("ISaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa")
                                     ->segments->read();
        } catch (DeserializeException $e) {}
          catch (TwilioException $e) {}

        $this->assertRequest(new Request(
            'get',
            'https://notify.twilio.com/v1/Services/ISaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa/Segments'
        ));
    }

    public function testReadEmptyResponse() {
        $this->holodeck->mock(new Response(
            200,
            '
            {
                "meta": {
                    "first_page_url": "https://notify.twilio.com/v1/Services/ISaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa/Segments?PageSize=50&Page=0",
                    "key": "segments",
                    "next_page_url": null,
                    "page": 0,
                    "page_size": 50,
                    "previous_page_url": null,
                    "url": "https://notify.twilio.com/v1/Services/ISaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa/Segments?PageSize=50&Page=0"
                },
                "segments": []
            }
            '
        ));

        $actual = $this->twilio->notify->v1->services("ISaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa")
                                           ->segments->read();

        $this->assertNotNull($actual);
    }

    public function testReadFullResponse() {
        $this->holodeck->mock(new Response(
            200,
            '
            {
                "segments": [
                    {
                        "account_sid": "ACaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa",
                        "date_created": "2017-02-14T14:36:41Z",
                        "date_updated": "2017-02-14T14:36:41Z",
                        "service_sid": "ISaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa",
                        "sid": "GSaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa",
                        "unique_name": "segment"
                    }
                ],
                "meta": {
                    "first_page_url": "https://notify.twilio.com/v1/Services/ISaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa/Segments?PageSize=50&Page=0",
                    "key": "segments",
                    "next_page_url": null,
                    "page": 0,
                    "page_size": 50,
                    "previous_page_url": null,
                    "url": "https://notify.twilio.com/v1/Services/ISaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa/Segments?PageSize=50&Page=0"
                }
            }
            '
        ));

        $actual = $this->twilio->notify->v1->services("ISaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa")
                                           ->segments->read();

        $this->assertGreaterThan(0, count($actual));
    }
}