<?php

namespace AdvisingApp\Prospect\Observers;

use App\Models\User;
use AdvisingApp\Prospect\Models\Prospect;

class ProspectObserver
{
    public function creating(Prospect $prospect): void
    {
        $user = auth()->user();

        if ($user instanceof User) {
            $prospect->createdBy()->associate($user);
        }
    }
}
