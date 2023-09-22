<?php

namespace Assist\Alert\Models;

use App\Models\BaseModel;
use Assist\Alert\Enums\AlertStatus;
use Assist\Alert\Enums\AlertSeverity;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Assist\Notifications\Models\Contracts\Subscribable;
use Assist\Audit\Models\Concerns\Auditable as AuditableTrait;
use Assist\Notifications\Models\Contracts\CanTriggerAutoSubscription;

/**
 * @mixin IdeHelperAlert
 */
class Alert extends BaseModel implements Auditable, CanTriggerAutoSubscription
{
    use SoftDeletes;
    use AuditableTrait;

    protected $fillable = [
        'concern_id',
        'concern_type',
        'description',
        'severity',
        'suggested_intervention',
    ];

    protected $casts = [
        'severity' => AlertSeverity::class,
        'status' => AlertStatus::class,
    ];

    public function concern(): MorphTo
    {
        return $this->morphTo();
    }

    public function getSubscribable(): ?Subscribable
    {
        return $this->concern instanceof Subscribable ? $this->concern : null;
    }

    public function scopeStatus(Builder $query, AlertStatus $status)
    {
        $query->where('status', $status);
    }
}
