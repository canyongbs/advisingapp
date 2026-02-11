<?php

/*
<COPYRIGHT>

    Copyright © 2016-2026, Canyon GBS LLC. All rights reserved.

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

namespace AdvisingApp\Task\Models;

use AdvisingApp\Audit\Models\Concerns\Auditable as AuditableTrait;
use AdvisingApp\Notification\Models\Contracts\CanTriggerAutoSubscription;
use AdvisingApp\Notification\Models\Contracts\Subscribable;
use AdvisingApp\Project\Models\Project;
use AdvisingApp\Prospect\Models\Prospect;
use AdvisingApp\StudentDataModel\Models\Concerns\BelongsToEducatable;
use AdvisingApp\StudentDataModel\Models\Scopes\LicensedToEducatable;
use AdvisingApp\StudentDataModel\Models\Student;
use AdvisingApp\Task\Database\Factories\TaskFactory;
use AdvisingApp\Task\Enums\TaskStatus;
use AdvisingApp\Task\Histories\TaskHistory;
use AdvisingApp\Task\Models\Scopes\ConfidentialTaskScope;
use AdvisingApp\Task\Observers\TaskObserver;
use AdvisingApp\Team\Models\Team;
use AdvisingApp\Timeline\Models\Concerns\InteractsWithHistory;
use AdvisingApp\Timeline\Models\Contracts\HasHistory;
use App\Models\BaseModel;
use App\Models\User;
use Bvtterfly\ModelStateMachine\HasStateMachine;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasVersion4Uuids as HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Collection;
use OwenIt\Auditing\Contracts\Auditable;

/**
 * @property-read Student|Prospect $concern
 *
 * @mixin IdeHelperTask
 */
#[ObservedBy([TaskObserver::class])] #[ScopedBy([ConfidentialTaskScope::class])]
class Task extends BaseModel implements Auditable, CanTriggerAutoSubscription, HasHistory
{
    use BelongsToEducatable;

    /** @use HasFactory<TaskFactory> */
    use HasFactory;

    use HasUuids;
    use AuditableTrait;
    use SoftDeletes;
    use HasStateMachine;
    use InteractsWithHistory;

    protected $fillable = [
        'title',
        'description',
        'due',
        'concern_id',
        'concern_type',
        'is_confidential',
    ];

    protected $casts = [
        'status' => TaskStatus::class,
        'due' => 'datetime',
        'is_confidential' => 'boolean',
    ];

    /**
     * @param string $event
     * @param Collection<string, string> $old
     * @param Collection<string, string> $new
     * @param Collection<int, array<string, mixed>> $pending
     *
     * @return void
     */
    public function processCustomHistories(string $event, Collection $old, Collection $new, Collection $pending): void
    {
        if ($event !== 'updated') {
            return;
        }

        if ($new->has('status')) {
            $this->recordHistory('status_changed', $old->only('status'), $new->only('status'), $pending);
            $new->forget('status');
        }

        if ($new->has('assigned_to')) {
            $this->recordHistory('reassigned', $old->only('assigned_to'), $new->only('assigned_to'), $pending);
            $new->forget('assigned_to');
        }
    }

    /**
     * @return array<string>
     */
    public function getStateMachineFields(): array
    {
        return [
            'status',
        ];
    }

    /** @return MorphTo<Model, $this> */
    public function concern(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * @return MorphMany<TaskHistory, $this>
     */
    public function histories(): MorphMany
    {
        return $this->morphMany(TaskHistory::class, 'subject');
    }

    /**
     * @return BelongsTo<Project, $this>
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * @return BelongsToMany<User, $this, covariant ConfidentialTasksUsers>
     */
    public function confidentialAccessUsers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'confidential_task_users')
            ->using(ConfidentialTasksUsers::class)
            ->withTimestamps();
    }

    /**
     * @return BelongsToMany<Team, $this, covariant ConfidentialTasksTeams>
     */
    public function confidentialAccessTeams(): BelongsToMany
    {
        return $this->belongsToMany(Team::class, 'confidential_task_teams')
            ->using(ConfidentialTasksTeams::class)
            ->withTimestamps();
    }

    /**
     * @return BelongsToMany<Project, $this, covariant ConfidentialTasksProjects>
     */
    public function confidentialAccessProjects(): BelongsToMany
    {
        return $this->belongsToMany(Project::class, 'confidential_task_projects')
            ->using(ConfidentialTasksProjects::class)
            ->withTimestamps();
    }

    public function getSubscribable(): ?Subscribable
    {
        return $this->concern;
    }

    public function scopeByNextDue(Builder $query): void
    {
        $query->orderBy('due');
    }

    public function scopeOpen(Builder $query): void
    {
        $query->where('status', '=', TaskStatus::Pending)
            ->orWhere('status', '=', TaskStatus::InProgress);
    }

    protected static function booted(): void
    {
        static::addGlobalScope('licensed', function (Builder $builder) {
            $builder->tap(new LicensedToEducatable('concern'));
        });
    }
}
