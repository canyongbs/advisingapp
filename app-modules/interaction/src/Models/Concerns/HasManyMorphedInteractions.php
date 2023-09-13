<?php

namespace Assist\Interaction\Models\Concerns;

use Assist\Interaction\Models\Interaction;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait HasManyMorphedInteractions
{
    public function interactions(): MorphMany
    {
        return $this->morphMany(
            related: Interaction::class,
            name: 'interactable',
        );
    }
}
