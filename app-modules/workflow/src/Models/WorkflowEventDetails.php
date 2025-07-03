<?php

namespace AdvisingApp\Workflow\Models;

use AdvisingApp\Audit\Models\Concerns\Auditable as AuditableTrait;
use AdvisingApp\Workflow\Models\Contracts\WorkflowAction;
use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Concerns\HasVersion4Uuids as HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

class WorkflowEventDetails extends BaseModel implements Auditable, WorkflowAction
{
    use SoftDeletes;
    use AuditableTrait;
    use HasUuids;

    protected $fillable = [
        'event_id',
    ];

    /**
     * @return BelongsTo<WorkflowStep, $this>
     */
    public function workflowStep(): BelongsTo
    {
        return $this->belongsTo(WorkflowStep::class);
    }
}
