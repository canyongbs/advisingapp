<?php

/*
<COPYRIGHT>

    Copyright © 2016-2024, Canyon GBS LLC. All rights reserved.

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

namespace AdvisingApp\CareTeam\Observers;

use App\Models\User;
use Illuminate\Support\HtmlString;
use Filament\Notifications\Notification;
use AdvisingApp\CareTeam\Models\CareTeam;
use AdvisingApp\Prospect\Models\Prospect;
use AdvisingApp\StudentDataModel\Models\Student;
use AdvisingApp\Prospect\Filament\Resources\ProspectResource;
use AdvisingApp\Notification\Events\TriggeredAutoSubscription;
use AdvisingApp\StudentDataModel\Filament\Resources\StudentResource;

class CareTeamObserver
{
    public function created(CareTeam $careTeam): void
    {
        $user = $careTeam->user;

        if ($user instanceof User) {
            TriggeredAutoSubscription::dispatch($user, $careTeam);

            $educatable = $careTeam->educatable;

            $name = $educatable->{$educatable->displayNameKey()};

            $target = match ($educatable::class) {
                Prospect::class => ProspectResource::class,
                Student::class => StudentResource::class,
            };

            $url = $target::getUrl('view', ['record' => $educatable]);

            $link = new HtmlString("<a href='{$url}' target='_blank' class='underline'>{$name}</a>");

            $morph = str($educatable->getMorphClass());

            $user->notify(
                Notification::make()
                    ->success()
                    ->title("You have been added as a care team member to {$morph} {$link}")
                    ->toDatabase(),
            );
        }
    }

    public function deleted(CareTeam $careTeam): void
    {
        $user = $careTeam->user;

        if ($user instanceof User) {
            $educatable = $careTeam->educatable;

            $name = $educatable->{$educatable->displayNameKey()};

            $target = match ($educatable::class) {
                Prospect::class => ProspectResource::class,
                Student::class => StudentResource::class,
            };

            $url = $target::getUrl('view', ['record' => $educatable]);

            $link = new HtmlString("<a href='{$url}' target='_blank' class='underline'>{$name}</a>");

            $morph = str($educatable->getMorphClass());

            $user->notify(
                Notification::make()
                    ->danger()
                    ->title("You have been removed from the care team for {$morph} {$link}")
                    ->toDatabase(),
            );
        }
    }
}
