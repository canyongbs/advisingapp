<?php

namespace AdvisingApp\Prospect\Observers;

use AdvisingApp\Prospect\Models\Prospect;
use App\Models\User;

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
