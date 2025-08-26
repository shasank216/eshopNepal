<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class AlphaSmsService
{
    protected $baseUrl;
    protected $apiKey;
    protected $senderId;

    public function __construct()
    {
        $this->baseUrl = config('services.alpha_sms.base_url');
        $this->apiKey = config('services.alpha_sms.key');
        $this->senderId = config('services.alpha_sms.senderid');
    }

    /**
     * Send SMS using Alpha SMS API.
     *
     * @param string $contacts  Comma-separated list of phone numbers
     * @param string $message   The message content
     * @param string $campaign  The campaign identifier
     * @param string $routeId   The route identifier
     * @return \Illuminate\Http\Client\Response
     */
    public function sendSms(string $contacts, string $message)
    {
        // Prepare the GET request parameters
        $queryParams = [
            'key' => $this->apiKey,
            'campaign' => 8786,
            'routeid' => 135,
            'type' => 'text',
            'contacts' => $contacts,
            'senderid' => $this->senderId,
            'msg' => $message,
            'responsetype' => 'json',
        ];

        //  $response= Http::get($this->baseUrl, $queryParams);
        // \Log::info("SMS Response: " . json_encode($response));
        // return $response;
        // Send GET request to Alpha SMS API
        return Http::get($this->baseUrl, $queryParams);
    }
}
