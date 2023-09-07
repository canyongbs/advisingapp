<?php

namespace Assist\ServiceManagement\Models;

use Eloquent;
use App\Models\User;
use DateTimeInterface;
use App\Models\BaseModel;
use App\Models\Institution;
use Assist\Audit\Models\Audit;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Kirschbaum\PowerJoins\PowerJoins;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Assist\Notifications\Models\Contracts\Subscribable;
use Assist\AssistDataModel\Models\Contracts\Identifiable;
use Illuminate\Database\UniqueConstraintViolationException;
use Assist\Audit\Models\Concerns\Auditable as AuditableTrait;
use Assist\Interaction\Models\Concerns\HasManyMorphedInteractions;
use Assist\Notifications\Models\Contracts\CanTriggerAutoSubscription;
use Assist\ServiceManagement\Exceptions\ServiceRequestNumberExceededReRollsException;
use Assist\ServiceManagement\Services\ServiceRequestNumber\Contracts\ServiceRequestNumberGenerator;

/**
 * Assist\ServiceManagement\Models\ServiceRequest
 *
 * @property string $id
 * @property string $service_request_number
 * @property string|null $respondent_type
 * @property string|null $respondent_id
 * @property string|null $close_details
 * @property string|null $res_details
 * @property string|null $institution_id
 * @property string|null $status_id
 * @property string|null $type_id
 * @property string|null $priority_id
 * @property string|null $assigned_to_id
 * @property string|null $created_by_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property-read User|null $assignedTo
 * @property-read Collection<int, Audit> $audits
 * @property-read int|null $audits_count
 * @property-read User|null $createdBy
 * @property-read Institution|null $institution
 * @property-read \Assist\ServiceManagement\Models\ServiceRequestPriority|null $priority
 * @property-read Model|\Eloquent $respondent
 * @property-read Collection<int, \Assist\ServiceManagement\Models\ServiceRequestUpdate> $serviceRequestUpdates
 * @property-read int|null $service_request_updates_count
 * @property-read \Assist\ServiceManagement\Models\ServiceRequestStatus|null $status
 * @property-read \Assist\ServiceManagement\Models\ServiceRequestType|null $type
 *
 * @method static \Assist\ServiceManagement\Database\Factories\ServiceRequestFactory factory($count = null, $state = [])
 * @method static Builder|ServiceRequest newModelQuery()
 * @method static Builder|ServiceRequest newQuery()
 * @method static Builder|ServiceRequest onlyTrashed()
 * @method static Builder|ServiceRequest query()
 * @method static Builder|ServiceRequest whereAssignedToId($value)
 * @method static Builder|ServiceRequest whereCloseDetails($value)
 * @method static Builder|ServiceRequest whereCreatedAt($value)
 * @method static Builder|ServiceRequest whereCreatedById($value)
 * @method static Builder|ServiceRequest whereDeletedAt($value)
 * @method static Builder|ServiceRequest whereId($value)
 * @method static Builder|ServiceRequest whereInstitutionId($value)
 * @method static Builder|ServiceRequest wherePriorityId($value)
 * @method static Builder|ServiceRequest whereResDetails($value)
 * @method static Builder|ServiceRequest whereRespondentId($value)
 * @method static Builder|ServiceRequest whereRespondentType($value)
 * @method static Builder|ServiceRequest whereServiceRequestNumber($value)
 * @method static Builder|ServiceRequest whereStatusId($value)
 * @method static Builder|ServiceRequest whereTypeId($value)
 * @method static Builder|ServiceRequest whereUpdatedAt($value)
 * @method static Builder|ServiceRequest withTrashed()
 * @method static Builder|ServiceRequest withoutTrashed()
 *
 * @mixin Eloquent
 */
class ServiceRequest extends BaseModel implements Auditable, CanTriggerAutoSubscription, Identifiable
{
    use SoftDeletes;
    use PowerJoins;
    use AuditableTrait;
    use HasUuids;
    use HasManyMorphedInteractions;

    protected $fillable = [
        'respondent_type',
        'respondent_id',
        'institution_id',
        'status_id',
        'type_id',
        'priority_id',
        'assigned_to_id',
        'close_details',
        'res_details',
        'created_by_id',
    ];

    public function save(array $options = [])
    {
        $attempts = 0;

        do {
            try {
                DB::beginTransaction();

                $save = parent::save($options);
            } catch (UniqueConstraintViolationException $e) {
                $attempts++;
                $save = false;

                if ($attempts < 3) {
                    $this->service_request_number = app(ServiceRequestNumberGenerator::class)->generate();
                }

                DB::rollBack();

                if ($attempts >= 3) {
                    throw new ServiceRequestNumberExceededReRollsException(
                        previous: $e,
                    );
                }

                continue;
            }

            DB::commit();

            break;
        } while ($attempts < 3);

        return $save;
    }

    public function identifier(): string
    {
        return $this->id;
    }

    public function getSubscribable(): ?Subscribable
    {
        return $this->respondent instanceof Subscribable ? $this->respondent : null;
    }

    public function respondent(): MorphTo
    {
        return $this->morphTo(
            name: 'respondent',
            type: 'respondent_type',
            id: 'respondent_id',
        );
    }

    public function institution(): BelongsTo
    {
        return $this->belongsTo(Institution::class, 'institution_id');
    }

    public function serviceRequestUpdates(): HasMany
    {
        return $this->hasMany(ServiceRequestUpdate::class, 'service_request_id');
    }

    public function status(): BelongsTo
    {
        return $this->belongsTo(ServiceRequestStatus::class);
    }

    public function type(): BelongsTo
    {
        return $this->belongsTo(ServiceRequestType::class);
    }

    public function priority(): BelongsTo
    {
        return $this->belongsTo(ServiceRequestPriority::class);
    }

    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    protected function serializeDate(DateTimeInterface $date): string
    {
        return $date->format(config('project.datetime_format') ?? 'Y-m-d H:i:s');
    }
}
