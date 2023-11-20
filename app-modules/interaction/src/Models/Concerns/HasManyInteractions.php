<?php

namespace Assist\Interaction\Models\Concerns;

use Assist\Interaction\Models\Interaction;
use Illuminate\Database\Eloquent\Relations\HasMany;

trait HasManyInteractions
{
    public function interactions(): HasMany
    {
        return $this->hasMany(Interaction::class);
    }
}
