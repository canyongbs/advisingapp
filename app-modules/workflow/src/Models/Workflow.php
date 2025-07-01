<?php

namespace AdvisingApp\Workflow\Models;

use AdvisingApp\Audit\Models\Concerns\Auditable as AuditableTrait;
use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Concerns\HasVersion4Uuids as HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

class Workflow extends BaseModel implements Auditable
{
    use SoftDeletes;
    use AuditableTrait;
    use HasUuids;

    protected $casts = [
        'is_enabled' => 'boolean',
    ];

    /**
     * @return BelongsTo<WorkflowTrigger, $this>
     */
    public function workflowTrigger(): BelongsTo
    {
        return $this->belongsTo(WorkflowTrigger::class);
    }

    /**
     * @return HasMany<WorkflowStep, $this>
     */
    public function workflowSteps(): HasMany
    {
        return $this->hasMany(WorkflowStep::class);
    }
}
