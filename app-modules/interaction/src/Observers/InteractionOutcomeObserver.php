<?php

namespace AdvisingApp\Interaction\Observers;

use Laravel\Pennant\Feature;
use App\Features\InteractionDefaultsFeature;
use AdvisingApp\Interaction\Models\InteractionOutcome;

class InteractionOutcomeObserver
{
    public function saving(InteractionOutcome $interactionOutcome): void
    {
        if (Feature::active(InteractionDefaultsFeature::class) && $interactionOutcome->is_default) {
            InteractionOutcome::query()
                ->where('is_default', true)
                ->update(['is_default' => false]);
        }
    }
}
