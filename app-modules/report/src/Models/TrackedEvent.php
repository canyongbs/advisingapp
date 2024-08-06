<?php

namespace AdvisingApp\Report\Models;

use App\Models\BaseModel;
use AdvisingApp\Report\Enums\TrackedEventType;

/**
 * @mixin IdeHelperTrackedEvent
 */
class TrackedEvent extends BaseModel
{
    public $timestamps = false;

    protected $fillable = [
        'type',
        'occurred_at',
    ];

    protected $casts = [
        'type' => TrackedEventType::class,
    ];
}
