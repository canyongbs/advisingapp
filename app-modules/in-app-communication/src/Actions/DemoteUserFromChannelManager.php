<?php

/*
<COPYRIGHT>

    Copyright © 2016-2025, Canyon GBS LLC. All rights reserved.

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

use AdvisingApp\InAppCommunication\Enums\ConversationType;
use AdvisingApp\InAppCommunication\Models\TwilioConversation;
use App\Models\User;
use Exception;
use Twilio\Rest\Client;

class DemoteUserFromChannelManager
{
    public function __construct(
        public Client $twilioClient,
    ) {}

    public function __invoke(User $user, TwilioConversation $conversation): void
    {
        throw_unless(
            $conversation->type === ConversationType::Channel,
            new Exception('Only channels have managers.')
        );

        throw_unless(
            $conversation->participants()->whereKey($user)->exists(),
            new Exception('User is not a participant in the channel.')
        );

        throw_unless(
            $conversation->managers()->whereKeyNot($user->getKey())->exists(),
            'User cannot be removed because they are the only manager in the channel.'
        );

        $conversation->managers()
            ->updateExistingPivot($user->getKey(), [
                'is_channel_manager' => false,
            ]);
    }
}
