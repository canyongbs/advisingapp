<?php

namespace AdvisingApp\CareTeam\Observers;

use App\Models\User;
use AdvisingApp\CareTeam\Models\CareTeam;
use AdvisingApp\Notification\Events\TriggeredAutoSubscription;
use AdvisingApp\Prospect\Filament\Resources\ProspectResource;
use AdvisingApp\Prospect\Models\Prospect;
use AdvisingApp\StudentDataModel\Filament\Resources\StudentResource;
use AdvisingApp\StudentDataModel\Models\Student;
use Filament\Notifications\Notification;
use Illuminate\Support\HtmlString;

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
