<?php

namespace AdvisingApp\InAppCommunication\Events;

use App\Models\User;
use Carbon\CarbonInterface;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use AdvisingApp\InAppCommunication\Models\TwilioConversation;

class ConversationMessageSent
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public readonly CarbonInterface $messageSentAt;

    public function __construct(
        readonly public TwilioConversation $conversation,
        readonly public User $author,
        readonly public string $messageSid,
        readonly public array $messageContent,
    ) {
        $this->messageSentAt = now();
    }
}
