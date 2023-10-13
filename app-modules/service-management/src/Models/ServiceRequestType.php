<?php

namespace Assist\ServiceManagement\Models;

use DateTimeInterface;
use App\Models\BaseModel;
use OwenIt\Auditing\Contracts\Auditable;
use OpenSearch\ScoutDriverPlus\Searchable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Assist\Audit\Models\Concerns\Auditable as AuditableTrait;

/**
 * @mixin IdeHelperServiceRequestType
 */
class ServiceRequestType extends BaseModel implements Auditable
{
    use SoftDeletes;
    use HasUuids;
    use AuditableTrait;
    use Searchable;

    protected $fillable = [
        'name',
    ];

    /**
     * @return array{id: mixed}
     */
    public function toSearchableArray(): array
    {
        return [
            'id' => $this->getScoutKey(),
            'name' => $this->name,
        ];
    }

    public function serviceRequests(): HasMany
    {
        return $this->hasMany(ServiceRequest::class, 'type_id');
    }

    protected function serializeDate(DateTimeInterface $date): string
    {
        return $date->format(config('project.datetime_format') ?? 'Y-m-d H:i:s');
    }
}
