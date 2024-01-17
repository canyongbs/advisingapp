<?php

namespace AdvisingApp\ServiceManagement\Models;

use App\Models\BaseModel;
use OwenIt\Auditing\Contracts\Auditable;
use AdvisingApp\Audit\Models\Concerns\Auditable as AuditableTrait;
use AdvisingApp\ServiceManagement\Enums\SystemChangeRequestClassification;

class ChangeRequestStatus extends BaseModel implements Auditable
{
    use AuditableTrait;

    protected $fillable = [
        'name',
        'classification',
    ];

    protected $casts = [
        'classification' => SystemChangeRequestClassification::class,
    ];
}
