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

namespace AdvisingApp\Workflow\Models;

use AdvisingApp\Audit\Models\Concerns\Auditable as AuditableTrait;
use AdvisingApp\Workflow\Filament\Blocks\InteractionBlock;
use AdvisingApp\Workflow\Filament\Blocks\WorkflowActionBlock;
use AdvisingApp\Workflow\Jobs\ExecuteWorkflowActionJob;
use AdvisingApp\Workflow\Jobs\InteractionWorkflowActionJob;
use Illuminate\Database\Eloquent\Concerns\HasVersion4Uuids as HasUuids;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

/**
 * @mixin IdeHelperWorkflowInteractionDetails
 */
class WorkflowInteractionDetails extends WorkflowDetails implements Auditable
{
    use SoftDeletes;
    use AuditableTrait;
    use HasUuids;

    protected $fillable = [
        'interaction_initiative_id',
        'interaction_driver_id',
        'division_id',
        'interaction_outcome_id',
        'interaction_relation_id',
        'interaction_status_id',
        'interaction_type_id',
        'start_datetime',
        'end_datetime',
        'subject',
        'description',
        'workflow_step_id',
    ];

    protected $casts = [
        'start_datetime' => 'datetime',
        'end_datetime' => 'datetime',
    ];

    public function getLabel(): string
    {
        return 'Interaction';
    }

    public function getBlock(): WorkflowActionBlock
    {
        return InteractionBlock::make();
    }

    public function getActionExecutableJob(WorkflowRunStep $workflowRunStep): ExecuteWorkflowActionJob
    {
        return new InteractionWorkflowActionJob($workflowRunStep);
    }

    public function getType(): string
    {
        return 'interaction';
    }

    public function hasBeenExecuted(): bool
    {
        $workflowRunSteps = WorkflowRun::whereWorkflowTriggerId($this->workflowStep->workflow->workflowTrigger->getKey())->first()->workflowRunSteps;

        return ! is_null($workflowRunSteps->where('details_type', WorkflowActionType::Interaction)->where('details_id', $this->id)->first()->dispatched_at);
    }
}
