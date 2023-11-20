<?php

namespace Assist\Engagement\Models\Concerns;

use Assist\Engagement\Models\EngagementBatch;
use Illuminate\Database\Eloquent\Relations\HasMany;

trait HasManyEngagementBatches
{
    public function engagementBatches(): HasMany
    {
        return $this->hasMany(EngagementBatch::class);
    }
}
