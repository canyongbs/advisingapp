<?php

namespace Assist\Team\Observers;

use Assist\Team\Models\TeamUser;

class TeamUserObserver
{
    //TODO: remove this if we support multiple teams
    public function creating(TeamUser $teamUser)
    {
        if ($teamUser->user->teams()->count() > 0) {
            return false;
        }
    }
}
