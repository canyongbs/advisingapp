<?php

namespace Assist\Engagement\Models\Concerns;

use Assist\Engagement\Models\Engagement;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait HasManyMorphedEngagements
{
    public function engagements(): MorphMany
    {
        return $this->morphMany(
            related: Engagement::class,
            name: 'recipient',
        );
    }
}
