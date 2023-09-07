<?php

namespace Assist\Engagement\Models;

use Eloquent;
use App\Models\User;
use App\Models\BaseModel;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Assist\Notifications\Models\Contracts\Subscribable;
use Assist\Audit\Models\Concerns\Auditable as AuditableTrait;
use Assist\Notifications\Models\Contracts\CanTriggerAutoSubscription;

/**
 * Assist\Engagement\Models\Engagement
 *
 * @property string $id
 * @property string $user_id
 * @property string|null $recipient_id
 * @property string|null $recipient_type
 * @property string $subject
 * @property string|null $body
 * @property string $deliver_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection<int, \Assist\Audit\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read User $createdBy
 * @property-read Collection<int, \Assist\Engagement\Models\EngagementDeliverable> $deliverables
 * @property-read int|null $deliverables_count
 * @property-read Collection<int, \Assist\Engagement\Models\EngagementDeliverable> $engagementDeliverables
 * @property-read int|null $engagement_deliverables_count
 * @property-read Model|\Eloquent $recipient
 * @property-read User $user
 *
 * @method static \Assist\Engagement\Database\Factories\EngagementFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|Engagement hasNotBeenDelivered()
 * @method static \Illuminate\Database\Eloquent\Builder|Engagement newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Engagement newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Engagement query()
 * @method static \Illuminate\Database\Eloquent\Builder|Engagement whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Engagement whereDeliverAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Engagement whereBody($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Engagement whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Engagement whereRecipientId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Engagement whereRecipientType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Engagement whereSubject($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Engagement whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Engagement whereUserId($value)
 *
 * @mixin Eloquent
 */
class Engagement extends BaseModel implements Auditable, CanTriggerAutoSubscription
{
    use AuditableTrait;

    protected $fillable = [
        'user_id',
        'engagement_batch_id',
        'subject',
        'body',
        'recipient_id',
        'recipient_type',
        'deliver_at',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->user();
    }

    public function engagementDeliverables(): HasMany
    {
        return $this->hasMany(EngagementDeliverable::class);
    }

    public function deliverables(): HasMany
    {
        return $this->engagementDeliverables();
    }

    public function recipient(): MorphTo
    {
        return $this->morphTo(
            name: 'recipient',
            type: 'recipient_type',
            id: 'recipient_id',
        );
    }

    public function engagementBatch(): BelongsTo
    {
        return $this->belongsTo(EngagementBatch::class);
    }

    public function batch(): BelongsTo
    {
        return $this->engagementBatch();
    }

    public function scopeHasNotBeenDelivered(Builder $query): void
    {
        $query->whereDoesntHave('engagementDeliverables', function (Builder $query) {
            $query->whereNotNull('delivered_at');
        });
    }

    public function scopeIsNotPartOfABatch(Builder $query): void
    {
        $query->whereNull('batch_id');
    }

    public function hasBeenDelivered(): bool
    {
        return (bool) $this->deliverables->filter(fn (EngagementDeliverable $deliverable) => $deliverable->hasBeenDelivered())->count() > 0;
    }

    public function getSubscribable(): ?Subscribable
    {
        return $this->recipient instanceof Subscribable ? $this->recipient : null;
    }
}
