<?php

namespace Assist\Engagement\Actions;

use Assist\Engagement\Models\EngagementResponse;

class CreateEngagementResponse
{
    public function __invoke(array $data): void
    {
        logger('CreateEngagementResponse');
        logger($data);

        $findEngagementResponseSender = resolve(FindEngagementResponseSender::class);

        $sender = $findEngagementResponseSender($data['From']);

        EngagementResponse::create([
            'sender_id' => $sender->id,
            'sender_type' => $sender->getMorphClass(),
            'content' => $data['Body'],
            // TODO It doesn't look like this data comes in the payload from Twilio
            // 'sent_at' => $data['date_sent'],
        ]);
    }
}
