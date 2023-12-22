<?php

namespace AdvisingApp\MeetingCenter\Models;

use App\Models\BaseModel;

class Event extends BaseModel
{
    protected $fillable = [
        'title',
        'description',
        'location',
        'capacity',
        'starts_at',
        'ends_at',
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
    ];
}
