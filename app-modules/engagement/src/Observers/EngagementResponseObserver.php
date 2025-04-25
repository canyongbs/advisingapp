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

namespace AdvisingApp\Engagement\Observers;

use AdvisingApp\Engagement\Enums\EngagementResponseType;
use AdvisingApp\Engagement\Models\EngagementResponse;
use AdvisingApp\Prospect\Filament\Resources\ProspectResource\Pages\ViewProspect;
use AdvisingApp\Prospect\Models\Prospect;
use AdvisingApp\StudentDataModel\Filament\Resources\StudentResource\Pages\ViewStudent;
use AdvisingApp\StudentDataModel\Models\Student;
use AdvisingApp\Timeline\Events\TimelineableRecordCreated;
use AdvisingApp\Timeline\Events\TimelineableRecordDeleted;
use App\Models\User;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Model;

class EngagementResponseObserver
{
    public function created(EngagementResponse $response): void
    {
        /** @var Student|Prospect $entity */
        $entity = $response->sender;

        TimelineableRecordCreated::dispatch($entity, $response);

        $entity->subscribedUsers()
            ->each(function (User $user) use ($response, $entity) {
                $type = match ($response->type) {
                    EngagementResponseType::Email => 'email',
                    EngagementResponseType::Sms => 'text message',
                };

                $user->notifyNow(
                    Notification::make()
                        ->success()
                        ->title(match (true) {
                            $entity instanceof Student => "An inbound {$type} has been received for student <a href='" . ViewStudent::getUrl(['record' => $entity]) . "' target='_blank' class='underline'>{$entity[$entity->displayNameKey()]}</a> and has been placed in a new status.",
                            $entity instanceof Prospect => "An inbound {$type} has been received for prospect <a href='" . ViewProspect::getUrl(['record' => $entity]) . "' target='_blank' class='underline'>{$entity->full_name}</a> and has been placed in a new status.",
                        })
                        ->toDatabase(),
                );
            });
    }

    public function deleted(EngagementResponse $response): void
    {
        /** @var Model $entity */
        $entity = $response->sender;

        TimelineableRecordDeleted::dispatch($entity, $response);
    }
}
