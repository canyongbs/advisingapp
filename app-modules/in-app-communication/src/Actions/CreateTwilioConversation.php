<?php

namespace Assist\InAppCommunication\Actions;

use Twilio\Rest\Client;
use Assist\InAppCommunication\Enums\ConversationType;
use Assist\InAppCommunication\Models\TwilioConversation;

class CreateTwilioConversation
{
    public function __construct(
        public Client $twilioClient,
    ) {}

    public function __invoke(ConversationType $type, string $friendlyName = null): TwilioConversation
    {
        $conversation = $this->twilioClient->conversations->v1->conversations->create([
            'friendlyName' => $friendlyName,
            'attributes' => json_encode([
                'type' => $type->value,
            ]),
        ]);

        return TwilioConversation::create(
            [
                'sid' => $conversation->sid,
                'friendly_name' => $conversation->friendlyName,
                'type' => $type,
            ]
        );
    }
}
