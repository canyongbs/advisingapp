<?php

namespace Assist\Engagement\Models\Concerns;

use Assist\Engagement\Models\EngagementResponse;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait HasManyMorphedEngagementResponses
{
    public function engagementResponses(): MorphMany
    {
        return $this->morphMany(
            related: EngagementResponse::class,
            name: 'sender',
        );
    }
}
