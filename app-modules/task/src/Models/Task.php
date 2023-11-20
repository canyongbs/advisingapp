<?php

/*
<COPYRIGHT>

Copyright © 2022-2023, Canyon GBS LLC

All rights reserved.

This file is part of a project developed using Laravel, which is an open-source framework for PHP.
Canyon GBS LLC acknowledges and respects the copyright of Laravel and other open-source
projects used in the development of this solution.

This project is licensed under the Affero General Public License (AGPL) 3.0.
For more details, see https://github.com/canyongbs/assistbycanyongbs/blob/main/LICENSE.

Notice:
- The copyright notice in this file and across all files and applications in this
 repository cannot be removed or altered without violating the terms of the AGPL 3.0 License.
- The software solution, including services, infrastructure, and code, is offered as a
 Software as a Service (SaaS) by Canyon GBS LLC.
- Use of this software implies agreement to the license terms and conditions as stated
 in the AGPL 3.0 License.

For more information or inquiries please visit our website at
https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

namespace Assist\Task\Models;

use Exception;
use App\Models\User;
use App\Models\BaseModel;
use Assist\Task\Enums\TaskStatus;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Assist\Prospect\Models\Prospect;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Database\Eloquent\Builder;
use Assist\AssistDataModel\Models\Student;
use Assist\Campaign\Models\CampaignAction;
use Illuminate\Database\Eloquent\SoftDeletes;
use Bvtterfly\ModelStateMachine\HasStateMachine;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Assist\AssistDataModel\Models\Contracts\Educatable;
use Assist\Notifications\Models\Contracts\Subscribable;
use Assist\AssistDataModel\Models\Traits\EducatableScopes;
use Assist\Audit\Models\Concerns\Auditable as AuditableTrait;
use Assist\Campaign\Models\Contracts\ExecutableFromACampaignAction;
use Assist\Notifications\Models\Contracts\CanTriggerAutoSubscription;

/**
 * @property-read Student|Prospect $concern
 *
 * @mixin IdeHelperTask
 */
class Task extends BaseModel implements Auditable, CanTriggerAutoSubscription, ExecutableFromACampaignAction
{
    use HasFactory;
    use HasUuids;
    use AuditableTrait;
    use SoftDeletes;
    use HasStateMachine;
    use EducatableScopes;

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

    public function getWebPermissions(): Collection
    {
        return collect(['import', ...$this->webPermissions()]);
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

    public function getSubscribable(): ?Subscribable
    {
        return $this->concern instanceof Subscribable ? $this->concern : null;
    }

    public function scopeByNextDue(Builder $query): void
    {
        $query->orderBy('due', 'asc');
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
                ->caseload
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
}
