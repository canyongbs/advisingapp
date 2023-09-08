<?php

namespace Assist\Alert\Models;

use Eloquent;
use App\Models\BaseModel;
use Assist\Audit\Models\Audit;
use Illuminate\Support\Carbon;
use Assist\Alert\Enums\AlertSeverity;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Assist\Notifications\Models\Contracts\Subscribable;
use Assist\Audit\Models\Concerns\Auditable as AuditableTrait;
use Assist\Notifications\Models\Contracts\CanTriggerAutoSubscription;

/**
 * Assist\Alert\Models\Alert
 *
 * @property string $id
 * @property string $concern_type
 * @property string $concern_id
 * @property string $description
 * @property AlertSeverity $severity
 * @property string $suggested_intervention
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property-read Collection<int, Audit> $audits
 * @property-read int|null $audits_count
 * @property-read Model|\Eloquent $concern
 *
 * @method static \Assist\Alert\Database\Factories\AlertFactory factory($count = null, $state = [])
 * @method static Builder|Alert newModelQuery()
 * @method static Builder|Alert newQuery()
 * @method static Builder|Alert onlyTrashed()
 * @method static Builder|Alert query()
 * @method static Builder|Alert whereConcernId($value)
 * @method static Builder|Alert whereConcernType($value)
 * @method static Builder|Alert whereCreatedAt($value)
 * @method static Builder|Alert whereDeletedAt($value)
 * @method static Builder|Alert whereDescription($value)
 * @method static Builder|Alert whereId($value)
 * @method static Builder|Alert whereSeverity($value)
 * @method static Builder|Alert whereSuggestedIntervention($value)
 * @method static Builder|Alert whereUpdatedAt($value)
 * @method static Builder|Alert withTrashed()
 * @method static Builder|Alert withoutTrashed()
 *
 * @mixin Eloquent
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
    ];

    public function concern(): MorphTo
    {
        return $this->morphTo();
    }

    public function getSubscribable(): ?Subscribable
    {
        return $this->concern instanceof Subscribable ? $this->concern : null;
    }
}
