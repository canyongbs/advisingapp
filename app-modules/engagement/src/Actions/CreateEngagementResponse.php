<?php

namespace Assist\Engagement\Actions;

use Assist\Engagement\Models\EngagementResponse;
use Assist\Engagement\DataTransferObjects\EngagementResponseData;
use Assist\Engagement\Actions\Contracts\EngagementResponseSenderFinder;

class CreateEngagementResponse
{
    public function __construct(
        public EngagementResponseSenderFinder $finder
    ) {}

    public function __invoke(EngagementResponseData $data): void
    {
        $sender = $this->finder->find($data->from);

        if (! is_null($sender)) {
            EngagementResponse::create([
                // TODO Need to handle this better, perhaps some getter on the Prospect/Student
                'sender_id' => $sender->id ?? $sender->sisid,
                'sender_type' => $sender->getMorphClass(),
                'content' => $data->body,
                // TODO We might need to retroactively get this data from the Twilio API
                // For now, we will assume that the message was sent at the time it was received
                'sent_at' => now(),
            ]);
        }
    }
}
