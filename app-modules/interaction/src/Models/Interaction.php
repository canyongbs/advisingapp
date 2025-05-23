<?php

/*
<COPYRIGHT>

    Copyright © 2016-2025, Canyon GBS LLC. All rights reserved.

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

use AdvisingApp\Audit\Models\Concerns\Auditable as AuditableTrait;
use AdvisingApp\CaseManagement\Models\CaseModel;
use AdvisingApp\Division\Models\Division;
use AdvisingApp\Interaction\Models\Scopes\InteractionConfidentialScope;
use AdvisingApp\Interaction\Observers\InteractionObserver;
use AdvisingApp\Notification\Models\Contracts\CanTriggerAutoSubscription;
use AdvisingApp\Notification\Models\Contracts\Subscribable;
use AdvisingApp\Prospect\Models\Prospect;
use AdvisingApp\StudentDataModel\Models\Concerns\BelongsToEducatable;
use AdvisingApp\StudentDataModel\Models\Scopes\LicensedToEducatable;
use AdvisingApp\StudentDataModel\Models\Student;
use AdvisingApp\Team\Models\Team;
use AdvisingApp\Timeline\Models\Contracts\ProvidesATimeline;
use AdvisingApp\Timeline\Models\Timeline;
use AdvisingApp\Timeline\Timelines\InteractionTimeline;
use App\Models\Authenticatable;
use App\Models\BaseModel;
use App\Models\User;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Collection;
use OwenIt\Auditing\Contracts\Auditable;

/**
 * @mixin IdeHelperInteraction
 */
#[ObservedBy([InteractionObserver::class])] #[ScopedBy(InteractionConfidentialScope::class)]
class Interaction extends BaseModel implements Auditable, CanTriggerAutoSubscription, ProvidesATimeline
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
        'is_confidential',
    ];

    protected $casts = [
        'start_datetime' => 'datetime',
        'end_datetime' => 'datetime',
        'is_confidential' => 'boolean',
    ];

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function getSubscribable(): ?Subscribable
    {
        return $this->interactable instanceof Subscribable ? $this->interactable : null;
    }

    /**
     * @return MorphTo<Model, $this>
     */
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

    /**
     * @return BelongsTo<InteractionInitiative, $this>
     */
    public function initiative(): BelongsTo
    {
        return $this->belongsTo(InteractionInitiative::class, 'interaction_initiative_id');
    }

    /**
     * @return BelongsTo<InteractionDriver, $this>
     */
    public function driver(): BelongsTo
    {
        return $this->belongsTo(InteractionDriver::class, 'interaction_driver_id');
    }

    /**
     * @return BelongsTo<Division, $this>
     */
    public function division(): BelongsTo
    {
        return $this->belongsTo(Division::class, 'division_id');
    }

    /**
     * @return BelongsTo<InteractionOutcome, $this>
     */
    public function outcome(): BelongsTo
    {
        return $this->belongsTo(InteractionOutcome::class, 'interaction_outcome_id');
    }

    /**
     * @return BelongsTo<InteractionRelation, $this>
     */
    public function relation(): BelongsTo
    {
        return $this->belongsTo(InteractionRelation::class, 'interaction_relation_id');
    }

    /**
     * @return BelongsTo<InteractionStatus, $this>
     */
    public function status(): BelongsTo
    {
        return $this->belongsTo(InteractionStatus::class, 'interaction_status_id');
    }

    /**
     * @return BelongsTo<InteractionType, $this>
     */
    public function type(): BelongsTo
    {
        return $this->belongsTo(InteractionType::class, 'interaction_type_id');
    }

    /**
     * @return BelongsToMany<User, $this, covariant InteractionConfidentialUser>
     */
    public function confidentialAccessUsers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'interaction_confidential_users')
            ->using(InteractionConfidentialUser::class)
            ->withTimestamps();
    }

    /**
     * @return BelongsToMany<Team, $this, covariant InteractionConfidentialTeam>
     */
    public function confidentialAccessTeams(): BelongsToMany
    {
        return $this->belongsToMany(Team::class, 'interaction_confidential_teams')
            ->using(InteractionConfidentialTeam::class)
            ->withTimestamps();
    }

    protected static function booted(): void
    {
        static::addGlobalScope('licensed', function (Builder $builder) {
            if (! auth()->check()) {
                return;
            }

            /** @var Authenticatable $user */
            $user = auth()->user();

            $caseRespondentTypeColumn = app(CaseModel::class)->respondent()->getMorphType();

            $builder
                ->where(fn (Builder $query) => $query
                    ->tap(new LicensedToEducatable('interactable'))
                    ->when(
                        ! $user->hasLicense(Student::getLicenseType()),
                        fn (Builder $query) => $query->where(fn (Builder $query) => $query->whereHasMorph(
                            'interactable',
                            CaseModel::class,
                            fn (Builder $query) => $query->where($caseRespondentTypeColumn, '!=', app(Student::class)->getMorphClass()),
                        )->orWhere(
                            'interactable_type',
                            '!=',
                            app(CaseModel::class)->getMorphClass(),
                        )),
                    )
                    ->when(
                        ! $user->hasLicense(Prospect::getLicenseType()),
                        fn (Builder $query) => $query->where(fn (Builder $query) => $query->whereHasMorph(
                            'interactable',
                            CaseModel::class,
                            fn (Builder $query) => $query->where($caseRespondentTypeColumn, '!=', app(Prospect::class)->getMorphClass()),
                        )->orWhere(
                            'interactable_type',
                            '!=',
                            app(CaseModel::class)->getMorphClass(),
                        )),
                    ));
        });
    }
}
