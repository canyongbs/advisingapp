<?php

namespace Assist\Engagement\Models;

use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphPivot;
use Assist\Notifications\Models\Contracts\Subscribable;
use Assist\Notifications\Models\Contracts\CanTriggerAutoSubscription;

/**
 * Assist\Engagement\Models\EngagementFileEntities
 *
 * @property string $engagement_file_id
 * @property string $entity_id
 * @property string $entity_type
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Assist\Engagement\Models\EngagementFile|null $engagementFile
 * @property-read \Illuminate\Database\Eloquent\Model|\Eloquent $entity
 *
 * @method static \Illuminate\Database\Eloquent\Builder|EngagementFileEntities newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EngagementFileEntities newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EngagementFileEntities query()
 * @method static \Illuminate\Database\Eloquent\Builder|EngagementFileEntities whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EngagementFileEntities whereEngagementFileId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EngagementFileEntities whereEntityId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EngagementFileEntities whereEntityType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EngagementFileEntities whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 * @mixin IdeHelperEngagementFileEntities
 */
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
