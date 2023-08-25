<?php

namespace Assist\Engagement\Models;

use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphPivot;
use Assist\Notifications\Models\Contracts\Subscribable;
use Assist\Notifications\Models\Contracts\CanTriggerAutoSubscription;

class EngagementFileEntities extends MorphPivot implements CanTriggerAutoSubscription
{
    protected $table = 'engagement_file_entities';

    public function getSubscribable(): ?Subscribable
    {
        return $this->entity instanceof Subscribable ? $this->entity : null;
    }

    public function entity(): MorphTo
    {
        return $this->morphTo();
    }

    public function engagementFile(): BelongsTo
    {
        return $this->belongsTo(EngagementFile::class);
    }
}
