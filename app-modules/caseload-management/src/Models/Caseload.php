<?php

namespace Assist\CaseloadManagement\Models;

use App\Models\BaseModel;
use Assist\CaseloadManagement\Enums\CaseloadSubject;

class Caseload extends BaseModel
{
    protected $fillable = [
        'query',
        'filters',
        'name',
        'model',
        'type',
    ];

    protected $casts = [
        'filters' => 'array',
        // 'model' => CaseloadSubject::class,
    ];
}
