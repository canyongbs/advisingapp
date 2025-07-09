<?php

namespace AdvisingApp\Workflow\Models;

use AdvisingApp\Audit\Models\Concerns\Auditable as AuditableTrait;
use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Concerns\HasVersion4Uuids as HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

class WorkflowRunStepRelated extends BaseModel implements Auditable
{
    use SoftDeletes;
    use AuditableTrait;
    use HasUuids;

    /**
     * @return BelongsTo<WorkflowRun, $this>
     */
    public function workflowRun(): BelongsTo
    {
        return $this->belongsTo(WorkflowRun::class);
    }
}
