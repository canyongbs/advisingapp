<?php

namespace Assist\Timeline\Models;

use App\Models\BaseModel;
use Assist\Prospect\Models\Prospect;
use Illuminate\Database\Eloquent\Builder;
use Assist\AssistDataModel\Models\Student;
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

    public function scopeForEducatable(Builder $query, Student|Prospect $educatable)
    {
        return $query->where('entity_type', $educatable->getMorphClass())
            ->where('entity_id', $educatable->getKey());
    }
}
