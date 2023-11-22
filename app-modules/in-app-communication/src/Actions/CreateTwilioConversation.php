<?php

/*
<COPYRIGHT>

Copyright © 2022-2023, Canyon GBS LLC

All rights reserved.

This file is part of a project developed using Laravel, which is an open-source framework for PHP.
Canyon GBS LLC acknowledges and respects the copyright of Laravel and other open-source
projects used in the development of this solution.

This project is licensed under the Affero General Public License (AGPL) 3.0.
For more details, see https://github.com/canyongbs/assistbycanyongbs/blob/main/LICENSE.

Notice:
- The copyright notice in this file and across all files and applications in this
 repository cannot be removed or altered without violating the terms of the AGPL 3.0 License.
- The software solution, including services, infrastructure, and code, is offered as a
 Software as a Service (SaaS) by Canyon GBS LLC.
- Use of this software implies agreement to the license terms and conditions as stated
 in the AGPL 3.0 License.

For more information or inquiries please visit our website at
https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

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
