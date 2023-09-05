<?php

namespace Assist\Case\Models;

use Eloquent;
use DateTimeInterface;
use App\Models\BaseModel;
use Assist\Audit\Models\Audit;
use Illuminate\Support\Carbon;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Assist\Audit\Models\Concerns\Auditable as AuditableTrait;
use Assist\Case\Database\Factories\ServiceRequestStatusFactory;

/**
 * Assist\Case\Models\ServiceRequestStatus
 *
 * @property string $id
 * @property string $name
 * @property string $color
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property-read Collection<int, Audit> $audits
 * @property-read int|null $audits_count
 * @property-read Collection<int, ServiceRequest> $caseItems
 * @property-read int|null $case_items_count
 *
 * @method static ServiceRequestStatusFactory factory($count = null, $state = [])
 * @method static Builder|ServiceRequestStatus newModelQuery()
 * @method static Builder|ServiceRequestStatus newQuery()
 * @method static Builder|ServiceRequestStatus onlyTrashed()
 * @method static Builder|ServiceRequestStatus query()
 * @method static Builder|ServiceRequestStatus whereColor($value)
 * @method static Builder|ServiceRequestStatus whereCreatedAt($value)
 * @method static Builder|ServiceRequestStatus whereDeletedAt($value)
 * @method static Builder|ServiceRequestStatus whereId($value)
 * @method static Builder|ServiceRequestStatus whereName($value)
 * @method static Builder|ServiceRequestStatus whereUpdatedAt($value)
 * @method static Builder|ServiceRequestStatus withTrashed()
 * @method static Builder|ServiceRequestStatus withoutTrashed()
 *
 * @mixin Eloquent
 */
class ServiceRequestStatus extends BaseModel implements Auditable
{
    use SoftDeletes;
    use HasUuids;
    use AuditableTrait;

    protected $fillable = [
        'name',
        'color',
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
