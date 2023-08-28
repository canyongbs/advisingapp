<?php

namespace Assist\Task\Models;

use App\Models\User;
use App\Models\BaseModel;
use Assist\Task\Enums\TaskStatus;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Assist\Notifications\Models\Contracts\Subscribable;
use Assist\Audit\Models\Concerns\Auditable as AuditableTrait;
use Assist\Notifications\Models\Contracts\CanTriggerAutoSubscription;

class Task extends BaseModel implements Auditable, CanTriggerAutoSubscription
{
    use HasFactory;
    use HasUuids;
    use AuditableTrait;
    use SoftDeletes;

    protected $fillable = [
        'description',
        'status',
        'due',
        'assigned_to',
        'concern_id',
        'concern_type',
    ];

    protected $casts = [
        'status' => TaskStatus::class,
        'due' => 'datetime',
    ];

    public function concern(): MorphTo
    {
        return $this->morphTo();
    }

    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function getSubscribable(): ?Subscribable
    {
        return $this->concern instanceof Subscribable ? $this->concern : null;
    }
}
