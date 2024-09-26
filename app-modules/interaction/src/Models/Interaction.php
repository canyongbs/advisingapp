<?php

/*
<COPYRIGHT>

    Copyright © 2016-2024, Canyon GBS LLC. All rights reserved.

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

namespace AdvisingApp\Interaction\Models;

use Exception;
use App\Models\User;
use App\Models\BaseModel;
use App\Models\Authenticatable;
use OwenIt\Auditing\Contracts\Auditable;
use AdvisingApp\Division\Models\Division;
use AdvisingApp\Prospect\Models\Prospect;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;
use AdvisingApp\Campaign\Models\CampaignAction;
use AdvisingApp\StudentDataModel\Models\Student;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use AdvisingApp\ServiceManagement\Models\ServiceRequest;
use AdvisingApp\Notification\Models\Contracts\Subscribable;
use AdvisingApp\StudentDataModel\Models\Contracts\Educatable;
use AdvisingApp\Audit\Models\Concerns\Auditable as AuditableTrait;
use AdvisingApp\StudentDataModel\Models\Scopes\LicensedToEducatable;
use AdvisingApp\StudentDataModel\Models\Concerns\BelongsToEducatable;
use AdvisingApp\Campaign\Models\Contracts\ExecutableFromACampaignAction;
use AdvisingApp\Interaction\History\InteractionHistory;
use AdvisingApp\Notification\Models\Contracts\CanTriggerAutoSubscription;
use AdvisingApp\Timeline\Models\Contracts\ProvidesATimeline;
use AdvisingApp\Timeline\Models\Timeline;
use AdvisingApp\Timeline\Timelines\InteractionHistoryTimeline;
use AdvisingApp\Timeline\Timelines\InteractionTimeline;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Support\Collection;

/**
 * @mixin IdeHelperInteraction
 */
class Interaction extends BaseModel implements Auditable, CanTriggerAutoSubscription, ExecutableFromACampaignAction, ProvidesATimeline
{
    use AuditableTrait;
    use BelongsToEducatable;
    use SoftDeletes;

    protected $fillable = [
        'description',
        'division_id',
        'end_datetime',
        'interactable_id',
        'interactable_type',
        'interaction_driver_id',
        'interaction_initiative_id',
        'interaction_outcome_id',
        'interaction_relation_id',
        'interaction_status_id',
        'interaction_type_id',
        'start_datetime',
        'subject',
        'user_id',
    ];

    protected $casts = [
        'start_datetime' => 'datetime',
        'end_datetime' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function getSubscribable(): ?Subscribable
    {
        return $this->interactable instanceof Subscribable ? $this->interactable : null;
    }

    public function interactable(): MorphTo
    {
        return $this->morphTo(
            name: 'interactable',
            type: 'interactable_type',
            id: 'interactable_id',
        );
    }

    public function timelineRecord(): MorphOne
    {
        return $this->morphOne(Timeline::class, 'timelineable');
    }

    public function timeline(): InteractionTimeline
    {
        return new InteractionTimeline($this);
    }

    public static function getTimelineData(Model $forModel): Collection
    {
        return $forModel->orderedInteractions()->get();
    }

    public function initiative(): BelongsTo
    {
        return $this->belongsTo(InteractionInitiative::class, 'interaction_initiative_id');
    }

    public function driver(): BelongsTo
    {
        return $this->belongsTo(InteractionDriver::class, 'interaction_driver_id');
    }

    public function division(): BelongsTo
    {
        return $this->belongsTo(Division::class, 'division_id');
    }

    public function outcome(): BelongsTo
    {
        return $this->belongsTo(InteractionOutcome::class, 'interaction_outcome_id');
    }

    public function relation(): BelongsTo
    {
        return $this->belongsTo(InteractionRelation::class, 'interaction_relation_id');
    }

    public function status(): BelongsTo
    {
        return $this->belongsTo(InteractionStatus::class, 'interaction_status_id');
    }

    public function type(): BelongsTo
    {
        return $this->belongsTo(InteractionType::class, 'interaction_type_id');
    }

    public static function executeFromCampaignAction(CampaignAction $action): bool|string
    {
        try {
            $action
                ->campaign
                ->segment
                ->retrieveRecords()
                ->each(function (Educatable $educatable) use ($action) {
                    $interactionData = [
                        'user_id' => $action->campaign->user_id,
                        'interactable_type' => $educatable->getMorphClass(),
                        'interactable_id' => $educatable->getKey(),
                        'interaction_type_id' => $action->data['interaction_type_id'],
                        'interaction_initiative_id' => $action->data['interaction_initiative_id'],
                        'interaction_relation_id' => $action->data['interaction_relation_id'],
                        'interaction_driver_id' => $action->data['interaction_driver_id'],
                        'interaction_status_id' => $action->data['interaction_status_id'],
                        'interaction_outcome_id' => $action->data['interaction_outcome_id'],
                        'division_id' => $action->data['division_id'],
                    ];

                    Interaction::create($interactionData);
                });

            return true;
        } catch (Exception $e) {
            return $e->getMessage();
        }

        // Do we need to be able to relate campaigns/actions to the RESULT of their actions?
    }

    protected static function booted(): void
    {
        static::addGlobalScope('licensed', function (Builder $builder) {
            if (! auth()->check()) {
                return;
            }

            /** @var Authenticatable $user */
            $user = auth()->user();

            $serviceRequestRespondentTypeColumn = app(ServiceRequest::class)->respondent()->getMorphType();

            $builder
                ->where(fn (Builder $query) => $query
                    ->tap(new LicensedToEducatable('interactable'))
                    ->when(
                        ! $user->hasLicense(Student::getLicenseType()),
                        fn (Builder $query) => $query->whereHasMorph(
                            'interactable',
                            ServiceRequest::class,
                            fn (Builder $query) => $query->where($serviceRequestRespondentTypeColumn, '!=', app(Student::class)->getMorphClass()),
                        ),
                    )
                    ->when(
                        ! $user->hasLicense(Prospect::getLicenseType()),
                        fn (Builder $query) => $query->whereHasMorph(
                            'interactable',
                            ServiceRequest::class,
                            fn (Builder $query) => $query->where($serviceRequestRespondentTypeColumn, '!=', app(Prospect::class)->getMorphClass()),
                        ),
                    ));
        });
    }
}
