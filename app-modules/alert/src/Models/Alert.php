<?php

namespace Assist\Alert\Models;

use App\Models\BaseModel;
use Assist\Alert\Enums\AlertStatus;
use Assist\Prospect\Models\Prospect;
use Assist\Alert\Enums\AlertSeverity;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Database\Eloquent\Builder;
use Assist\AssistDataModel\Models\Student;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Assist\Notifications\Models\Contracts\Subscribable;
use Assist\AssistDataModel\Models\Traits\EducatableScopes;
use Assist\Audit\Models\Concerns\Auditable as AuditableTrait;
use Assist\Notifications\Models\Contracts\CanTriggerAutoSubscription;

/**
 * @property-read Student|Prospect $concern
 *
 * @mixin IdeHelperAlert
 */
class Alert extends BaseModel implements Auditable, CanTriggerAutoSubscription
{
    use SoftDeletes;
    use AuditableTrait;
    use EducatableScopes;

    protected $fillable = [
        'concern_id',
        'concern_type',
        'description',
        'severity',
        'status',
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
