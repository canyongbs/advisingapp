<?php

namespace Assist\ServiceManagement\Models;

use Eloquent;
use DateTimeInterface;
use App\Models\BaseModel;
use Assist\Audit\Models\Audit;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Assist\Notifications\Models\Contracts\Subscribable;
use Assist\Audit\Models\Concerns\Auditable as AuditableTrait;
use Assist\ServiceManagement\Enums\ServiceRequestUpdateDirection;
use Assist\Notifications\Models\Contracts\CanTriggerAutoSubscription;

/**
 * Assist\ServiceManagement\Models\ServiceRequestUpdate
 *
 * @property string $id
 * @property string|null $service_request_id
 * @property string $update
 * @property bool $internal
 * @property ServiceRequestUpdateDirection $direction
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property-read Collection<int, Audit> $audits
 * @property-read int|null $audits_count
 * @property-read \Assist\ServiceManagement\Models\ServiceRequest|null $serviceRequest
 *
 * @method static \Assist\ServiceManagement\Database\Factories\ServiceRequestUpdateFactory factory($count = null, $state = [])
 * @method static Builder|ServiceRequestUpdate newModelQuery()
 * @method static Builder|ServiceRequestUpdate newQuery()
 * @method static Builder|ServiceRequestUpdate onlyTrashed()
 * @method static Builder|ServiceRequestUpdate query()
 * @method static Builder|ServiceRequestUpdate whereCreatedAt($value)
 * @method static Builder|ServiceRequestUpdate whereDeletedAt($value)
 * @method static Builder|ServiceRequestUpdate whereDirection($value)
 * @method static Builder|ServiceRequestUpdate whereId($value)
 * @method static Builder|ServiceRequestUpdate whereInternal($value)
 * @method static Builder|ServiceRequestUpdate whereServiceRequestId($value)
 * @method static Builder|ServiceRequestUpdate whereUpdate($value)
 * @method static Builder|ServiceRequestUpdate whereUpdatedAt($value)
 * @method static Builder|ServiceRequestUpdate withTrashed()
 * @method static Builder|ServiceRequestUpdate withoutTrashed()
 *
 * @mixin Eloquent
 * @mixin IdeHelperServiceRequestUpdate
 */
class ServiceRequestUpdate extends BaseModel implements Auditable, CanTriggerAutoSubscription
{
    use SoftDeletes;
    use HasUuids;
    use AuditableTrait;

    protected $fillable = [
        'service_request_id',
        'update',
        'internal',
        'direction',
    ];

    protected $casts = [
        'internal' => 'boolean',
        'direction' => ServiceRequestUpdateDirection::class,
    ];

    public function serviceRequest(): BelongsTo
    {
        return $this->belongsTo(ServiceRequest::class);
    }

    public function getSubscribable(): ?Subscribable
    {
        /** @var Subscribable|Model $respondent */
        $respondent = $this->serviceRequest->respondent;

        return $respondent instanceof Subscribable
            ? $respondent
            : null;
    }

    protected function serializeDate(DateTimeInterface $date): string
    {
        return $date->format(config('project.datetime_format') ?? 'Y-m-d H:i:s');
    }
}
