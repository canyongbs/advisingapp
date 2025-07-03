<?php

namespace AdvisingApp\Workflow\Models;

use AdvisingApp\Alert\Enums\AlertSeverity;
use AdvisingApp\Audit\Models\Concerns\Auditable as AuditableTrait;
use AdvisingApp\Workflow\Models\Contracts\WorkflowAction;
use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Concerns\HasVersion4Uuids as HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

/**
 * @mixin IdeHelperWorkflowProactiveAlertDetails
 */
class WorkflowProactiveAlertDetails extends BaseModel implements Auditable, WorkflowAction
{
    use SoftDeletes;
    use AuditableTrait;
    use HasUuids;

    protected $fillable = [
        'description',
        'severity',
        'suggested_intervention',
        'status_id',
    ];

    protected $casts = [
        'severity' => AlertSeverity::class,
    ];

    /**
     * @return BelongsTo<WorkflowStep, $this>
     */
    public function workflowStep(): BelongsTo
    {
        return $this->belongsTo(WorkflowStep::class);
    }
}
