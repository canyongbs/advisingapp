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

namespace AdvisingApp\Task\Models;

use Exception;
use App\Models\User;
use App\Models\BaseModel;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use AdvisingApp\Task\Enums\TaskStatus;
use OwenIt\Auditing\Contracts\Auditable;
use AdvisingApp\Prospect\Models\Prospect;
use Illuminate\Database\Eloquent\Builder;
use AdvisingApp\Task\Histories\TaskHistory;
use AdvisingApp\Task\Observers\TaskObserver;
use Illuminate\Database\Eloquent\SoftDeletes;
use AdvisingApp\Campaign\Models\CampaignAction;
use AdvisingApp\StudentDataModel\Models\Student;
use Bvtterfly\ModelStateMachine\HasStateMachine;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use AdvisingApp\Timeline\Models\Contracts\HasHistory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use AdvisingApp\Notification\Models\Contracts\Subscribable;
use AdvisingApp\StudentDataModel\Models\Contracts\Educatable;
use AdvisingApp\Timeline\Models\Concerns\InteractsWithHistory;
use AdvisingApp\Audit\Models\Concerns\Auditable as AuditableTrait;
use AdvisingApp\StudentDataModel\Models\Scopes\LicensedToEducatable;
use AdvisingApp\StudentDataModel\Models\Concerns\BelongsToEducatable;
use AdvisingApp\Campaign\Models\Contracts\ExecutableFromACampaignAction;
use AdvisingApp\Notification\Models\Contracts\CanTriggerAutoSubscription;

/**
 * @property-read Student|Prospect $concern
 *
 * @mixin IdeHelperTask
 */
#[ObservedBy([TaskObserver::class])]
class Task extends BaseModel implements Auditable, CanTriggerAutoSubscription, ExecutableFromACampaignAction, HasHistory
{
    use BelongsToEducatable;
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
    ];

    protected $casts = [
        'status' => TaskStatus::class,
        'due' => 'datetime',
    ];

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

    public function getStateMachineFields(): array
    {
        return [
            'status',
        ];
    }

    /** @return MorphTo<Educatable> */
    public function concern(): MorphTo
    {
        return $this->morphTo();
    }

    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function histories(): MorphMany
    {
        return $this->morphMany(TaskHistory::class, 'subject');
    }

    public function getSubscribable(): ?Subscribable
    {
        return $this->concern instanceof Subscribable ? $this->concern : null;
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

    public static function executeFromCampaignAction(CampaignAction $action): bool|string
    {
        try {
            DB::beginTransaction();

            $action
                ->campaign
                ->segment
                ->retrieveRecords()
                ->each(function (Educatable $educatable) use ($action) {
                    $task = new Task([
                        'title' => $action->data['title'],
                        'description' => $action->data['description'],
                        'due' => $action->data['due'],
                    ]);

                    $task->assignedTo()->associate($action->data['assigned_to']);
                    $task->createdBy()->associate($action->data['created_by']);
                    $task->concern()->associate($educatable);
                    $task->save();
                });

            DB::commit();

            return true;
        } catch (Exception $e) {
            DB::rollBack();

            return $e->getMessage();
        }

        // Do we need to be able to relate campaigns/actions to the RESULT of their actions?
    }

    protected static function booted(): void
    {
        static::addGlobalScope('licensed', function (Builder $builder) {
            $builder->tap(new LicensedToEducatable('concern'));
        });
    }
}
