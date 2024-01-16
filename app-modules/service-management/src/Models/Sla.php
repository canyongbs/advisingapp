<?php

namespace AdvisingApp\ServiceManagement\Models;

use App\Models\BaseModel;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\HasMany;
use AdvisingApp\Audit\Models\Concerns\Auditable as AuditableTrait;

class Sla extends BaseModel implements Auditable
{
    use SoftDeletes;
    use AuditableTrait;
    use HasUuids;

    protected $fillable = [
        'name',
        'description',
        'terms',
        'response_seconds',
        'resolution_seconds',
    ];

    protected $casts = [
        'response_seconds' => 'integer',
        'resolution_seconds' => 'integer',
    ];

    public function serviceRequestPriorities(): HasMany
    {
        return $this->hasMany(ServiceRequestPriority::class);
    }
}
