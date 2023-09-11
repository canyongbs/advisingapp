<?php

namespace Assist\Interaction\Models;

use App\Models\BaseModel;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Assist\Notifications\Models\Contracts\Subscribable;
use Assist\Audit\Models\Concerns\Auditable as AuditableTrait;
use Assist\Notifications\Models\Contracts\CanTriggerAutoSubscription;

/**
 * Assist\Interaction\Models\Interaction
 *
 * @property string $id
 * @property string|null $user_id
 * @property string|null $interactable_id
 * @property string|null $interactable_type
 * @property string|null $interaction_type_id
 * @property string|null $interaction_relation_id
 * @property string|null $interaction_campaign_id
 * @property string|null $interaction_driver_id
 * @property string|null $interaction_status_id
 * @property string|null $interaction_outcome_id
 * @property string|null $interaction_institution_id
 * @property \Illuminate\Support\Carbon $start_datetime
 * @property \Illuminate\Support\Carbon|null $end_datetime
 * @property string|null $subject
 * @property string|null $description
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Assist\Audit\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read \Assist\Interaction\Models\InteractionCampaign|null $campaign
 * @property-read \Assist\Interaction\Models\InteractionDriver|null $driver
 * @property-read \Illuminate\Database\Eloquent\Model|\Eloquent $interactable
 * @property-read \Assist\Interaction\Models\InteractionCampaign|null $interactionCampaign
 * @property-read \Assist\Interaction\Models\InteractionDriver|null $interactionDriver
 * @property-read \Assist\Interaction\Models\InteractionOutcome|null $interactionOutcome
 * @property-read \Assist\Interaction\Models\InteractionStatus|null $interactionStatus
 * @property-read \Assist\Interaction\Models\InteractionType|null $interactionType
 * @property-read \Assist\Interaction\Models\InteractionOutcome|null $outcome
 * @property-read \Assist\Interaction\Models\InteractionStatus|null $status
 * @property-read \Assist\Interaction\Models\InteractionType|null $type
 * @method static \Assist\Interaction\Database\Factories\InteractionFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|Interaction newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Interaction newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Interaction query()
 * @method static \Illuminate\Database\Eloquent\Builder|Interaction whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Interaction whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Interaction whereEndDatetime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Interaction whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Interaction whereInteractableId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Interaction whereInteractableType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Interaction whereInteractionCampaignId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Interaction whereInteractionDriverId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Interaction whereInteractionInstitutionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Interaction whereInteractionOutcomeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Interaction whereInteractionRelationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Interaction whereInteractionStatusId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Interaction whereInteractionTypeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Interaction whereStartDatetime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Interaction whereSubject($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Interaction whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Interaction whereUserId($value)
 * @mixin \Eloquent
 */
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
