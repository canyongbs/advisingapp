<?php

namespace AdvisingApp\Interaction\Observers;

use AdvisingApp\Interaction\Models\InteractionInitiative;

class InteractionInitiativesObserver
{
    public function saving(InteractionInitiative $interactionInitiative): void
    {
        if ($interactionInitiative->is_default) {
            InteractionInitiative::query()
                ->where('is_default', true)
                ->update(['is_default' => false]);
        }
    }
}
