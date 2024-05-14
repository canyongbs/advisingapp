<?php

namespace AdvisingApp\Interaction\Observers;

use AdvisingApp\Interaction\Models\InteractionType;

class InteractionTypesObserver
{
    public function saving(InteractionType $interactionType): void
    {
        if ($interactionType->is_default) {
            InteractionType::query()
                ->where('is_default', true)
                ->update(['is_default' => false]);
        }
    }
}
