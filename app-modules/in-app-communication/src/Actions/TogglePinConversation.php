<?php

namespace AdvisingApp\InAppCommunication\Actions;

use AdvisingApp\InAppCommunication\Models\TwilioConversation;
use AdvisingApp\InAppCommunication\Models\TwilioConversationUser;
use App\Models\User;
use Twilio\Rest\Client;

class TogglePinConversation
{
    public function __construct(
        public Client $twilioClient,
    ) {}

    public function __invoke(User $user, TwilioConversation $conversation): void
    {
        /** @var TwilioConversationUser $participant */
        $participant = $conversation->participant;

        $conversation->participants()
            ->updateExistingPivot($user->getKey(), [
                'is_pinned' => $participant->is_pinned = ! $participant->is_pinned,
            ]);
    }
}
