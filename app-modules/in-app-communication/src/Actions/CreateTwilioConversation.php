<?php

namespace Assist\InAppCommunication\Actions;

use Throwable;
use App\Models\User;
use Twilio\Rest\Client;
use Illuminate\Support\Collection;
use Twilio\Exceptions\TwilioException;
use Assist\InAppCommunication\Enums\ConversationType;
use Assist\InAppCommunication\Models\TwilioConversation;

class CreateTwilioConversation
{
    public function __construct(
        public Client $twilioClient,
    ) {}

    /**
     * @param ConversationType $type
     * @param string|null $friendlyName
     * @param Collection|null $users
     *
     * @throws Throwable
     * @throws TwilioException
     *
     * @return TwilioConversation
     */
    public function __invoke(ConversationType $type, ?string $friendlyName = null, ?Collection $users = null): TwilioConversation
    {
        if ($type === ConversationType::UserToUser) {
            throw_if(
                $users->count() !== 2,
                new TwilioException('User to User conversations must have 2 participants.')
            );

            $duplicateQuery = TwilioConversation::where('type', $type->value);

            $users->each(
                fn (User $user) => $duplicateQuery->whereHas(
                    'participants',
                    fn ($query) => $query->where('user_id', $user->id)
                )
            );

            throw_if(
                $duplicateQuery->exists(),
                new TwilioException('User to User conversations can only have 1 conversation per set of participants.')
            );
        }

        $twilioConversation = $this->twilioClient->conversations->v1->conversations->create([
            'friendlyName' => $friendlyName,
            'attributes' => json_encode([
                'type' => $type->value,
            ]),
        ]);

        $conversation = TwilioConversation::create(
            [
                'sid' => $twilioConversation->sid,
                'friendly_name' => $twilioConversation->friendlyName,
                'type' => $type,
            ]
        );

        $users?->each(fn (User $user) => app(AddUserToConversation::class)(user: $user, conversation: $conversation));

        return $conversation;
    }
}
