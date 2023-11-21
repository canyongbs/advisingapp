<?php

namespace Assist\InAppCommunication\Actions;

use Exception;
use App\Models\User;
use Twilio\Rest\Client;
use Assist\InAppCommunication\Enums\ConversationType;
use Assist\InAppCommunication\Models\TwilioConversation;

class AddUserToConversation
{
    public function __construct(
        public Client $twilioClient,
    ) {}

    public function __invoke(User $user, TwilioConversation $conversation): void
    {
        throw_if(
            $conversation->type === ConversationType::UserToUser && $conversation->participants()->count() >= 2,
            new Exception('User to User conversations can only have 2 participants.')
        );

        $participant = $this->twilioClient
            ->conversations
            ->v1
            ->conversations($conversation->sid)
            ->participants
            ->create(
                [
                    'identity' => $user->id,
                ]
            );

        $conversation->participants()->attach(
            $user,
            [
                'participant_sid' => $participant->sid,
            ]
        );
    }
}
