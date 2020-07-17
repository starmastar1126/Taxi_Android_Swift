<?php 
 
namespace Pubnub\Clients;

use Pubnub\PubnubUtil;
use Pubnub\PubnubException;


/**
 * Class DefaultClient
 *
 * Requests are executed immediately
 *
 * @package Pubnub\Requests
 */
class DefaultClient extends Client
{
    public function add(array $path, array $query)
    {
        parent::add($path, $query);

        return $this->execute();
    }

    private function execute()
    {
        $chs = $this->chs();
        $ch = $chs[0];

        $output = curl_exec($ch);
        $curlError = curl_errno($ch);
        $curlResponseCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlResponseURL = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
        $JSONDecodedResponse = PubnubUtil::json_decode($output);

        curl_close($ch);

        $this->requests = array();
        if ($JSONDecodedResponse != null)
            return $JSONDecodedResponse;
        elseif ($curlError == 28)
            return array(
                'error' => true,
                'service' => 'cURL',
                'status' => -1,
                'message' => 'request timeout',
                'payload' => "Pubnub request timeout. Maximum timeout: " . $this->curlSubscribeTimeout .
                    " second(s) for subscribe and " . $this->curlTimeout . " second(s) for non-subscribe requests. " .
                    "Requested URL: " . $curlResponseURL
            );
        else
            throw new PubnubException("Empty response from Pubnub. HTTP code: " . $curlResponseCode .
                ". cURL error code: " . $curlError .
                ". Requested URL: " . $curlResponseURL );
    }
}