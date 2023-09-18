<?php

namespace Assist\Division\Observers;

use Assist\Division\Models\Division;

class DivisionObserver
{
    public function creating(Division $division): void
    {
        $division->createdBy()->associate($division->createdBy ?? auth()->user());
        $division->updatedBy()->associate($division->updatedBy ?? auth()->user());
    }

    public function updating(Division $division): void
    {
        $division->updatedBy()->associate(auth()->user());
    }
}
