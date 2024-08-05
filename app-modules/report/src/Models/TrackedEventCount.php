<?php

namespace AdvisingApp\Report\Models;

use App\Models\BaseModel;
use AdvisingApp\Report\Enums\TrackedEventType;

class TrackedEventCount extends BaseModel
{
    protected $fillable = [
        'type',
        'count',
        'last_occurred_at',
    ];

    protected $casts = [
        'type' => TrackedEventType::class,
    ];
}
