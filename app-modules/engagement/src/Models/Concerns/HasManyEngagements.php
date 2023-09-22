<?php

namespace Assist\Engagement\Models\Concerns;

use Assist\Engagement\Models\Engagement;
use Illuminate\Database\Eloquent\Relations\HasMany;

trait HasManyEngagements
{
    public function engagements(): HasMany
    {
        return $this->hasMany(Engagement::class);
    }
}
