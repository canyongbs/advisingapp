<?php

namespace AdvisingApp\Interaction\Observers;

use Laravel\Pennant\Feature;
use App\Features\InteractionDefaultsFeature;
use AdvisingApp\Interaction\Models\InteractionRelation;

class InteractionRelationObserver
{
    public function saving(InteractionRelation $interactionRelation): void
    {
        if (Feature::active(InteractionDefaultsFeature::class) && $interactionRelation->is_default) {
            InteractionRelation::query()
                ->where('is_default', true)
                ->update(['is_default' => false]);
        }
    }
}
