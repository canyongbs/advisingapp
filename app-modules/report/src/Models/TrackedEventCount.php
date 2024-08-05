<?php

namespace AdvisingApp\Report\Models;

use AdvisingApp\Report\Enums\TrackedEventType;
use App\Models\BaseModel;

class TrackedEventCount extends BaseModel
{
    protected $fillable = [
        'type',
        'count',
    ];

    protected $casts = [
        'type' => TrackedEventType::class,
    ];
}
