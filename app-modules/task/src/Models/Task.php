<?php

namespace Assist\Task\Models;

use App\Models\User;
use App\Models\BaseModel;
use Assist\Task\Enums\TaskStatus;
use Assist\Prospect\Models\Prospect;
use OwenIt\Auditing\Contracts\Auditable;
use Assist\AssistDataModel\Models\Student;
use Illuminate\Database\Eloquent\SoftDeletes;
use Bvtterfly\ModelStateMachine\HasStateMachine;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Assist\AssistDataModel\Models\Contracts\Educatable;
use Assist\Notifications\Models\Contracts\Subscribable;
use Assist\Audit\Models\Concerns\Auditable as AuditableTrait;
use Assist\Notifications\Models\Contracts\CanTriggerAutoSubscription;

/**
 * @property-read Student|Prospect $concern
 *
 * @mixin IdeHelperTask
 */
class Task extends BaseModel implements Auditable, CanTriggerAutoSubscription
{
    use HasFactory;
    use HasUuids;
    use AuditableTrait;
    use SoftDeletes;
    use HasStateMachine;

    protected $fillable = [
        'description',
        'due',
        'concern_id',
        'concern_type',
    ];

    protected $casts = [
        'status' => TaskStatus::class,
        'due' => 'datetime',
    ];

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
}
