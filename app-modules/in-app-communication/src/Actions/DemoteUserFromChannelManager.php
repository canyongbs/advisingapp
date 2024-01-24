<?php

namespace AdvisingApp\InAppCommunication\Actions;

use Exception;
use App\Models\User;
use Twilio\Rest\Client;
use AdvisingApp\InAppCommunication\Enums\ConversationType;
use AdvisingApp\InAppCommunication\Models\TwilioConversation;

class DemoteUserFromChannelManager
{
    public function __construct(
        public Client $twilioClient,
    ) {}

    public function __invoke(User $user, TwilioConversation $conversation): void
    {
        throw_if(
            ($conversation->type === ConversationType::UserToUser),
            new Exception('Only channels have managers.')
        );

        throw_unless(
            $conversation->participants()->whereKey($user)->exists(),
            new Exception('User is not a participant in the channel.')
        );

        $conversation->participants()
            ->updateExistingPivot($user->getKey(), [
                'is_channel_manager' => false,
            ]);
    }
}
