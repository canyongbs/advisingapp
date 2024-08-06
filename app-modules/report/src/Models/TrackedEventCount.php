<?php

namespace AdvisingApp\Report\Models;

use App\Models\BaseModel;
use AdvisingApp\Report\Enums\TrackedEventType;

/**
 * @mixin IdeHelperTrackedEventCount
 */
class TrackedEventCount extends BaseModel
{
    protected $fillable = [
        'type',
        'count',
        'last_occurred_at',
    ];

    protected $casts = [
        'type' => TrackedEventType::class,
        'last_occurred_at' => 'datetime',
    ];
}
