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
use AdvisingApp\CaseManagement\Enums\CaseTypeAssignmentTypes;
use AdvisingApp\CaseManagement\Models\CasePriority;
use AdvisingApp\CaseManagement\Models\CaseStatus;
use AdvisingApp\CaseManagement\Models\CaseType;
use AdvisingApp\Division\Models\Division;
use AdvisingApp\Workflow\Database\Factories\WorkflowCaseDetailsFactory;
use AdvisingApp\Workflow\Filament\Blocks\CaseBlock;
use AdvisingApp\Workflow\Filament\Blocks\WorkflowActionBlock;
use AdvisingApp\Workflow\Jobs\CaseWorkflowActionJob;
use AdvisingApp\Workflow\Jobs\ExecuteWorkflowActionJob;
use App\Models\User;
use Illuminate\Database\Eloquent\Concerns\HasVersion4Uuids as HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

/**
 * @mixin IdeHelperWorkflowCaseDetails
 */
class WorkflowCaseDetails extends WorkflowDetails implements Auditable
{
    use SoftDeletes;
    use AuditableTrait;
    use HasUuids;

    /** @use HasFactory<WorkflowCaseDetailsFactory> */
    use HasFactory;

    protected $fillable = [
        'division_id',
        'status_id',
        'priority_id',
        'assigned_to_id',
        'close_details',
        'res_details',
    ];

    /**
     * @return BelongsTo<Division, $this>
     */
    public function division(): BelongsTo
    {
        return $this->belongsTo(Division::class);
    }

    /**
     * @return BelongsTo<CaseStatus, $this>
     */
    public function status(): BelongsTo
    {
        return $this->belongsTo(CaseStatus::class);
    }

    /**
     * @return BelongsTo<CasePriority, $this>
     */
    public function priority(): BelongsTo
    {
        return $this->belongsTo(CasePriority::class);
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to_id');
    }

    public function getLabel(): string
    {
        return 'Case';
    }

    public function getBlock(): WorkflowActionBlock
    {
        return CaseBlock::make();
    }

    public function getActionExecutableJob(WorkflowRunStep $workflowRunStep): ExecuteWorkflowActionJob
    {
        return new CaseWorkflowActionJob($workflowRunStep);
    }

    public function toArray(): array
    {
        $array = parent::toArray();

        if ($this->priority_id) {
            $priority = $this->relationLoaded('priority') ? $this->priority : $this->priority()->first();

            if ($priority && $priority->type_id) {
                $array['type_id'] = $priority->type_id;

                if (is_null($this->assigned_to_id)) {
                    $caseType = $priority->relationLoaded('type')
                        ? $priority->type
                        : CaseType::find($priority->type_id);

                    if ($caseType && $caseType->assignment_type !== CaseTypeAssignmentTypes::None) {
                        $array['assigned_to_id'] = 'automatic';
                    }
                }
            }
        }

        return $array;
    }
}
