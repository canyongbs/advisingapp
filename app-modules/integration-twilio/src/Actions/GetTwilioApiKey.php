<?php

namespace Assist\IntegrationTwilio\Actions;

use Twilio\Rest\Client;
use Assist\IntegrationTwilio\Settings\TwilioSettings;
use Assist\IntegrationTwilio\DataTransferObjects\TwilioApiKey;

class GetTwilioApiKey
{
    public function __construct(
        public Client $twilioClient,
        public TwilioSettings $twilioSettings,
    ) {}

    public function __invoke(): TwilioApiKey
    {
        $apiKey = $this->twilioSettings->api_key;

        if (empty($apiKey)) {
            $newKey = $this->twilioClient->newKeys
                ->create(['friendlyName' => config('app.name')]);

            $apiKey = $this->twilioSettings->api_key = new TwilioApiKey(
                api_sid: $newKey->sid,
                secret: $newKey->secret,
            );
            $this->twilioSettings->save();
        }

        return $apiKey;
    }
}
