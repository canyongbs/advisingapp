<?php

namespace Assist\MeetingCenter\Models;

use App\Models\BaseModel;

class Event extends BaseModel
{
    protected $fillable = [
        'title',
        'description',
        'starts_at',
        'ends_at'
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
    ];
}
