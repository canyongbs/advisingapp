<?php

/*
<COPYRIGHT>

    Copyright © 2022-2023, Canyon GBS LLC. All rights reserved.

    Advising App™ is licensed under the Elastic License 2.0. For more details,
    see https://github.com/canyongbs/advisingapp/blob/main/LICENSE.

    Notice:

    - You may not provide the software to third parties as a hosted or managed
      service, where the service provides users with access to any substantial set of
      the features or functionality of the software.
    - You may not move, change, disable, or circumvent the license key functionality
      in the software, and you may not remove or obscure any functionality in the
      software that is protected by the license key.
    - You may not alter, remove, or obscure any licensing, copyright, or other notices
      of the licensor in the software. Any use of the licensor’s trademarks is subject
      to applicable law.
    - Canyon GBS LLC respects the intellectual property rights of others and expects the
      same in return. Canyon GBS™ and Advising App™ are registered trademarks of
      Canyon GBS LLC, and we are committed to enforcing and protecting our trademarks
      vigorously.
    - The software solution, including services, infrastructure, and code, is offered as a
      Software as a Service (SaaS) by Canyon GBS LLC.
    - Use of this software implies agreement to the license terms and conditions as stated
      in the Elastic License 2.0.

    For more information or inquiries please visit our website at
    https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

namespace Assist\ServiceManagement\Models;

use Exception;
use App\Models\User;
use DateTimeInterface;
use App\Models\BaseModel;
use Illuminate\Support\Facades\DB;
use Assist\Division\Models\Division;
use Assist\Prospect\Models\Prospect;
use Kirschbaum\PowerJoins\PowerJoins;
use Assist\Auditing\Contracts\Auditable;
use Illuminate\Database\Eloquent\Builder;
use Assist\AssistDataModel\Models\Student;
use Assist\Campaign\Models\CampaignAction;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Staudenmeir\EloquentHasManyDeep\HasRelationships;
use Assist\AssistDataModel\Models\Contracts\Educatable;
use Assist\Notifications\Models\Contracts\Subscribable;
use Assist\AssistDataModel\Models\Contracts\Identifiable;
use Illuminate\Database\UniqueConstraintViolationException;
use Assist\Audit\Models\Concerns\Auditable as AuditableTrait;
use Assist\Interaction\Models\Concerns\HasManyMorphedInteractions;
use Assist\ServiceManagement\Enums\ServiceRequestAssignmentStatus;
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
    use HasRelationships;

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

    public function assignments(): HasMany
    {
        return $this->hasMany(ServiceRequestAssignment::class);
    }

    public function assignedTo(): HasOne
    {
        return $this->hasOne(ServiceRequestAssignment::class)
            ->latest('assigned_at')
            ->where('status', ServiceRequestAssignmentStatus::Active);
    }

    public function initialAssignment(): HasOne
    {
        return $this->hasOne(ServiceRequestAssignment::class)
            ->oldest('assigned_at');
    }

    public function histories(): HasMany
    {
        return $this->hasMany(ServiceRequestHistory::class);
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
                $request = ServiceRequest::create([
                    'respondent_type' => $educatable->getMorphClass(),
                    'respondent_id' => $educatable->getKey(),
                    'close_details' => $action->data['close_details'],
                    'res_details' => $action->data['res_details'],
                    'division_id' => $action->data['division_id'],
                    'status_id' => $action->data['status_id'],
                    'type_id' => $action->data['type_id'],
                    'priority_id' => $action->data['priority_id'],
                    'created_by_id' => $action->campaign->user->id,
                ]);

                if ($action->data['assigned_to_id']) {
                    $request->assignments()->create([
                        'user_id' => $action->data['assigned_to_id'],
                        'assigned_by_id' => $action->campaign->user->id,
                        'assigned_at' => now(),
                        'status' => ServiceRequestAssignmentStatus::Active,
                    ]);
                }
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
