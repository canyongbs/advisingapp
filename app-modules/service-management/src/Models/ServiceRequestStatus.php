<?php

namespace Assist\ServiceManagement\Models;

use DateTimeInterface;
use App\Models\BaseModel;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Assist\ServiceManagement\Enums\ColumnColorOptions;
use Assist\Audit\Models\Concerns\Auditable as AuditableTrait;
use Assist\ServiceManagement\Enums\SystemServiceRequestClassification;

/**
 * @mixin IdeHelperServiceRequestStatus
 */
class ServiceRequestStatus extends BaseModel implements Auditable
{
    use SoftDeletes;
    use HasUuids;
    use AuditableTrait;

    protected $fillable = [
        'classification',
        'name',
        'color',
    ];

    protected $casts = [
        'classification' => SystemServiceRequestClassification::class,
        'color' => ColumnColorOptions::class,
    ];

    public function serviceRequests(): HasMany
    {
        return $this->hasMany(ServiceRequest::class, 'status_id');
    }

    protected function serializeDate(DateTimeInterface $date): string
    {
        return $date->format(config('project.datetime_format') ?? 'Y-m-d H:i:s');
    }
}
