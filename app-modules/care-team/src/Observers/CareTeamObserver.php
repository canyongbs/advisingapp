<?php

namespace AdvisingApp\CareTeam\Observers;

use App\Models\User;
use AdvisingApp\CareTeam\Models\CareTeam;
use AdvisingApp\Notification\Events\TriggeredAutoSubscription;

class CareTeamObserver
{
    public function created(CareTeam $careTeam): void
    {
        $user = auth()->user();

        if ($user instanceof User) {
            TriggeredAutoSubscription::dispatch($user, $careTeam);
        }
    }
}
