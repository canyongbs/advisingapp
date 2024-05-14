<?php

namespace AdvisingApp\Interaction\Observers;

use AdvisingApp\Interaction\Models\InteractionDriver;

class InteractionDriverObserver
{
    public function saving(InteractionDriver $interactionDrivers): void
    {
        if ($interactionDrivers->is_default) {
            InteractionDriver::query()
                ->where('is_default', true)
                ->update(['is_default' => false]);
        }
    }
}
