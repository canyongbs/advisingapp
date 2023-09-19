<?php

namespace App\Models;

class Caseload extends BaseModel
{
    protected $fillable = [
        'query',
        'filters',
        'name',
        'model',
    ];

    protected $casts = [
        'filters' => 'array',
    ];
}
