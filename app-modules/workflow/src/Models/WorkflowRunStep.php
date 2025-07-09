<?php

namespace AdvisingApp\Workflow\Models;

use AdvisingApp\Audit\Models\Concerns\Auditable as AuditableTrait;
use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Concerns\HasVersion4Uuids as HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

class WorkflowRunStep extends BaseModel implements Auditable
{
    use SoftDeletes;
    use AuditableTrait;
    use HasUuids;

    protected $casts = [
        'started_at' => 'datetime',
        'dispatched_at' => 'datetime',
        'succeeded_at' => 'datetime',
        'last_failed_at' => 'datetime',
    ];

    /**
     * @return BelongsTo<WorkflowRunStep, $this>
     */
    public function workflowRunStep(): BelongsTo
    {
        return $this->belongsTo(WorkflowRunStep::class);
    }
}
