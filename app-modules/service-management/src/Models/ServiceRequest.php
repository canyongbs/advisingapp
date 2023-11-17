<?php

namespace Assist\ServiceManagement\Models;

use Exception;
use App\Models\User;
use DateTimeInterface;
use App\Models\BaseModel;
use Illuminate\Support\Facades\DB;
use Assist\Division\Models\Division;
use Assist\Prospect\Models\Prospect;
use Kirschbaum\PowerJoins\PowerJoins;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Database\Eloquent\Builder;
use Assist\AssistDataModel\Models\Student;
use Assist\Campaign\Models\CampaignAction;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Assist\AssistDataModel\Models\Contracts\Educatable;
use Assist\Notifications\Models\Contracts\Subscribable;
use Assist\AssistDataModel\Models\Contracts\Identifiable;
use Illuminate\Database\UniqueConstraintViolationException;
use Assist\Audit\Models\Concerns\Auditable as AuditableTrait;
use Assist\Interaction\Models\Concerns\HasManyMorphedInteractions;
use Assist\Campaign\Models\Contracts\ExecutableFromACampaignAction;
use Assist\Notifications\Models\Contracts\CanTriggerAutoSubscription;
use Assist\ServiceManagement\Enums\SystemServiceRequestClassification;
use Assist\ServiceManagement\Exceptions\ServiceRequestNumberExceededReRollsException;
use Assist\ServiceManagement\Services\ServiceRequestNumber\Contracts\ServiceRequestNumberGenerator;

/**
 * @property-read Student|Prospect $respondent
 *
 * @mixin IdeHelperServiceRequest
 */
class ServiceRequest extends BaseModel implements Auditable, CanTriggerAutoSubscription, Identifiable, ExecutableFromACampaignAction
{
    use SoftDeletes;
    use PowerJoins;
    use AuditableTrait;
    use HasUuids;
    use HasManyMorphedInteractions;

    protected $fillable = [
        'respondent_type',
        'respondent_id',
        'division_id',
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

    /** @return MorphTo<Educatable> */
    public function respondent(): MorphTo
    {
        return $this->morphTo(
            name: 'respondent',
            type: 'respondent_type',
            id: 'respondent_id',
        );
    }

    public function division(): BelongsTo
    {
        return $this->belongsTo(Division::class, 'division_id');
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

    public function scopeOpen(Builder $query): void
    {
        $query->whereIn(
            'status_id',
            ServiceRequestStatus::where('classification', SystemServiceRequestClassification::Open)->pluck('id')
        );
    }

    public static function executeFromCampaignAction(CampaignAction $action): bool|string
    {
        try {
            $action->campaign->caseload->retrieveRecords()->each(function (Educatable $educatable) use ($action) {
                ServiceRequest::create([
                    'respondent_type' => $educatable->getMorphClass(),
                    'respondent_id' => $educatable->getKey(),
                    'close_details' => $action->data['close_details'],
                    'res_details' => $action->data['res_details'],
                    'division_id' => $action->data['division_id'],
                    'status_id' => $action->data['status_id'],
                    'type_id' => $action->data['type_id'],
                    'priority_id' => $action->data['priority_id'],
                    'assigned_to_id' => $action->data['assigned_to_id'] ?? null,
                    'created_by_id' => $action->campaign->user->id,
                ]);
            });

            return true;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    protected function serializeDate(DateTimeInterface $date): string
    {
        return $date->format(config('project.datetime_format') ?? 'Y-m-d H:i:s');
    }
}
