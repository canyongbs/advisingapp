<?php

namespace Assist\Timeline\Models;

use App\Models\BaseModel;
use Assist\Prospect\Models\Prospect;
use Illuminate\Database\Eloquent\Builder;
use Assist\AssistDataModel\Models\Student;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Timeline extends BaseModel
{
    protected $fillable = [
        'educatable_type',
        'educatable_id',
        'timelineable_type',
        'timelineable_id',
        'record_creation',
    ];

    public function timelineable(): MorphTo
    {
        return $this->morphTo();
    }

    public function scopeForEducatable(Builder $query, Student|Prospect $educatable)
    {
        return $query->where('educatable_type', $educatable->getMorphClass())
            ->where('educatable_id', $educatable->getKey());
    }
}
