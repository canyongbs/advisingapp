<?php

namespace Assist\Timeline\Models;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * @mixin IdeHelperTimeline
 */
class Timeline extends BaseModel
{
    protected $fillable = [
        'entity_type',
        'entity_id',
        'timelineable_type',
        'timelineable_id',
        'record_sortable_date',
    ];

    public function timelineable(): MorphTo
    {
        return $this->morphTo();
    }

    public function scopeForEntity(Builder $query, Model $entity)
    {
        return $query->where('entity_type', $entity->getMorphClass())
            ->where('entity_id', $entity->getKey());
    }
}
