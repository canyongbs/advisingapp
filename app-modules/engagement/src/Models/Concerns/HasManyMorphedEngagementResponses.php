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

    public function orderedEngagementResponses(): MorphMany
    {
        return $this->engagementResponses()
            ->orderBy('sent_at', 'desc');
    }
}
