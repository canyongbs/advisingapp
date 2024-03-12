<?php

namespace AdvisingApp\Interaction\Observers;

use Laravel\Pennant\Feature;
use App\Features\InteractionDefaultsFeature;
use AdvisingApp\Interaction\Models\InteractionStatus;

class InteractionStatusObserver
{
    public function saving(InteractionStatus $interactionStatus): void
    {
        if (Feature::active(InteractionDefaultsFeature::class) && $interactionStatus->is_default) {
            InteractionStatus::query()
                ->where('is_default', true)
                ->update(['is_default' => false]);
        }
    }
}
