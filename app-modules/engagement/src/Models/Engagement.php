<?php

namespace Assist\Engagement\Models;

use App\Models\User;
use App\Models\BaseModel;
use Illuminate\Support\Collection;
use Assist\Prospect\Models\Prospect;
use Assist\Timeline\Models\Timeline;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use Assist\AssistDataModel\Models\Student;
use Assist\Timeline\Timelines\EngagementTimeline;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Assist\AssistDataModel\Models\Contracts\Educatable;
use Assist\Notifications\Models\Contracts\Subscribable;
use Assist\Timeline\Models\Contracts\ProvidesATimeline;
use Assist\Audit\Models\Concerns\Auditable as AuditableTrait;
use Assist\Notifications\Models\Contracts\CanTriggerAutoSubscription;

/**
 * @property-read Educatable $recipient
 *
 * @mixin IdeHelperEngagement
 */
class Engagement extends BaseModel implements Auditable, CanTriggerAutoSubscription, ProvidesATimeline
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

    protected $casts = [
        'deliver_at' => 'datetime',
    ];

    public function timelineRecord(): MorphOne
    {
        return $this->morphOne(Timeline::class, 'timelineable');
    }

    public function timeline(): EngagementTimeline
    {
        return new EngagementTimeline($this);
    }

    public static function getTimelineData(Model $forModel): Collection
    {
        return $forModel->orderedEngagements()->with(['deliverables', 'batch'])->get();
    }

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

    public function scopeHasBeenDelivered(Builder $query): void
    {
        $query->whereDoesntHave('engagementDeliverables', function (Builder $query) {
            $query->whereNull('delivered_at');
        });
    }

    public function scopeHasNotBeenDelivered(Builder $query): void
    {
        $query->whereDoesntHave('engagementDeliverables', function (Builder $query) {
            $query->whereNotNull('delivered_at');
        });
    }

    public function scopeIsNotPartOfABatch(Builder $query): void
    {
        $query->whereNull('engagement_batch_id');
    }

    public function scopeSentToStudent(Builder $query): void
    {
        $query->where('recipient_type', resolve(Student::class)->getMorphClass());
    }

    public function scopeSentToProspect(Builder $query): void
    {
        $query->where('recipient_type', resolve(Prospect::class)->getMorphClass());
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
