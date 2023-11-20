<?php

namespace Assist\Division\Observers;

use Assist\Division\Models\Division;

class DivisionObserver
{
    public function creating(Division $division): void
    {
        $division->createdBy()->associate($division->createdBy ?? auth()->user());
        $division->lastUpdatedBy()->associate($division->lastUpdatedBy ?? auth()->user());
    }

    public function updating(Division $division): void
    {
        $division->lastUpdatedBy()->associate(auth()->user());
    }
}
