<?php

namespace Assist\Engagement\Actions;

use Twilio\Rest\Client;

class EngagementSmsChannelDelivery extends QueuedEngagementDelivery
{
    public function deliver(): void
    {
        // TODO Extract Client
        $client = new Client(config('services.twilio.account_sid'), config('services.twilio.auth_token'));

        $client->messages->create(
            ! is_null(config('services.twilio.test_to_number')) ? config('services.twilio.test_to_number') : $this->deliverable->engagement->recipient->mobile,
            [
                'from' => config('services.twilio.from_number'),
                'body' => $this->deliverable->engagement->subject . "\n\n" . $this->deliverable->engagement->description,
            ]
        );

        // TODO Handle response
    }
}
