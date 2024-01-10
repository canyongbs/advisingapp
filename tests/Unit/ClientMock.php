<?php

namespace Tests\Unit;

use Twilio\Rest\Client;
use AllowDynamicProperties;
use Twilio\Http\Client as HttpClient;

#[AllowDynamicProperties]
class ClientMock extends Client
{
    public function __construct(
        $messageList,
        string $username = null,
        string $password = null,
        string $accountSid = null,
        string $region = null,
        HttpClient $httpClient = null,
        array $environment = null,
        array $userAgentExtensions = null,
    ) {
        parent::__construct(
            $username,
            $password,
            $accountSid,
            $region,
            $httpClient,
            $environment,
            $userAgentExtensions
        );
        $this->messages = $messageList;
    }
}
