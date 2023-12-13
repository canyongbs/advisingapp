<?php

/*
<COPYRIGHT>

    Copyright © 2022-2023, Canyon GBS LLC. All rights reserved.

    Advising App™ is licensed under the Elastic License 2.0. For more details,
    see https://github.com/canyongbs/advisingapp/blob/main/LICENSE.

    Notice:

    - You may not provide the software to third parties as a hosted or managed
      service, where the service provides users with access to any substantial set of
      the features or functionality of the software.
    - You may not move, change, disable, or circumvent the license key functionality
      in the software, and you may not remove or obscure any functionality in the
      software that is protected by the license key.
    - You may not alter, remove, or obscure any licensing, copyright, or other notices
      of the licensor in the software. Any use of the licensor’s trademarks is subject
      to applicable law.
    - Canyon GBS LLC respects the intellectual property rights of others and expects the
      same in return. Canyon GBS™ and Advising App™ are registered trademarks of
      Canyon GBS LLC, and we are committed to enforcing and protecting our trademarks
      vigorously.
    - The software solution, including services, infrastructure, and code, is offered as a
      Software as a Service (SaaS) by Canyon GBS LLC.
    - Use of this software implies agreement to the license terms and conditions as stated
      in the Elastic License 2.0.

    For more information or inquiries please visit our website at
    https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

namespace AdvisingApp\InAppCommunication\Actions;

use Throwable;
use App\Models\User;
use Twilio\Rest\Client;
use Illuminate\Support\Collection;
use Twilio\Exceptions\TwilioException;
use AdvisingApp\InAppCommunication\Enums\ConversationType;
use AdvisingApp\InAppCommunication\Models\TwilioConversation;

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
