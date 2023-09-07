<?php

namespace Assist\Interaction\Models;

use App\Models\BaseModel;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Assist\Notifications\Models\Contracts\Subscribable;
use Assist\Audit\Models\Concerns\Auditable as AuditableTrait;
use Assist\Notifications\Models\Contracts\CanTriggerAutoSubscription;

class Interaction extends BaseModel implements Auditable, CanTriggerAutoSubscription
{
    use AuditableTrait;

    protected $fillable = [
        'user_id',
        'interactable_id',
        'interactable_type',
        'interaction_campaign_id',
        'interaction_driver_id',
        'interaction_outcome_id',
        'interaction_status_id',
        'interaction_type_id',
        'start_datetime',
        'end_datetime',
        'subject',
        'description',
    ];

    protected $casts = [
        'start_datetime' => 'datetime',
        'end_datetime' => 'datetime',
    ];

    public function getSubscribable(): ?Subscribable
    {
        return $this->interactable instanceof Subscribable ? $this->interactable : null;
    }

    public function interactable(): MorphTo
    {
        return $this->morphTo(
            name: 'interactable',
            type: 'interactable_type',
            id: 'interactable_id',
        );
    }

    public function interactionCampaign(): BelongsTo
    {
        return $this->belongsTo(InteractionCampaign::class);
    }

    public function campaign(): BelongsTo
    {
        return $this->interactionCampaign();
    }

    public function interactionDriver(): BelongsTo
    {
        return $this->belongsTo(InteractionDriver::class);
    }

    public function driver(): BelongsTo
    {
        return $this->interactionDriver();
    }

    public function interactionOutcome(): BelongsTo
    {
        return $this->belongsTo(InteractionOutcome::class);
    }

    public function outcome(): BelongsTo
    {
        return $this->interactionOutcome();
    }

    public function interactionStatus(): BelongsTo
    {
        return $this->belongsTo(InteractionStatus::class);
    }

    public function status(): BelongsTo
    {
        return $this->interactionStatus();
    }

    public function interactionType(): BelongsTo
    {
        return $this->belongsTo(InteractionType::class);
    }

    public function type(): BelongsTo
    {
        return $this->interactionType();
    }
}
