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

        $sender = $findEngagementResponseSender($data['from']);

        EngagementResponse::create([
            'sender_id' => $sender->id,
            'sender_type' => $sender->getMorphClass(),
            'content' => $data['body'],
            'sent_at' => $data['date_sent'],
        ]);
    }
}
