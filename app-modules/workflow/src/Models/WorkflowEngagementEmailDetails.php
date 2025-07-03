<?php

namespace AdvisingApp\Workflow\Models;

use AdvisingApp\Audit\Models\Concerns\Auditable as AuditableTrait;
use AdvisingApp\Notification\Enums\NotificationChannel;
use AdvisingApp\Workflow\Models\Contracts\WorkflowAction;
use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Concerns\HasVersion4Uuids as HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

class WorkflowEngagementEmailDetails extends BaseModel implements Auditable, WorkflowAction
{
    use SoftDeletes;
    use AuditableTrait;
    use HasUuids;

    protected $fillable = [
        'channel',
        'subject',
        'body',
    ];

    protected $casts = [
        'channel' => NotificationChannel::class,
        'subject' => 'array',
        'body' => 'array',
    ];

    /**
     * @return BelongsTo<WorkflowStep, $this>
     */
    public function workflowStep(): BelongsTo
    {
        return $this->belongsTo(WorkflowStep::class);
    }
}
