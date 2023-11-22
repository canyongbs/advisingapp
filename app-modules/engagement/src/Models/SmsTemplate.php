<?php

namespace Assist\Engagement\Models;

use App\Models\BaseModel;

class SmsTemplate extends BaseModel
{
    protected $fillable = [
        'name',
        'description',
        'content',
    ];

    protected $casts = [
        'content' => 'array',
    ];
}
