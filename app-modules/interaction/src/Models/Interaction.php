<?php

namespace Assist\Interaction\Models;

use Exception;
use App\Models\BaseModel;
use Illuminate\Support\Collection;
use Assist\Division\Models\Division;
use App\Models\Contracts\IsSearchable;
use OwenIt\Auditing\Contracts\Auditable;
use Assist\Campaign\Models\CampaignAction;
use OpenSearch\ScoutDriverPlus\Searchable;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Assist\AssistDataModel\Models\Contracts\Educatable;
use Assist\Notifications\Models\Contracts\Subscribable;
use Assist\Audit\Models\Concerns\Auditable as AuditableTrait;
use Assist\Campaign\Models\Contracts\ExecutableFromACampaignAction;
use Assist\Notifications\Models\Contracts\CanTriggerAutoSubscription;

/**
 * @mixin IdeHelperInteraction
 */
class Interaction extends BaseModel implements Auditable, CanTriggerAutoSubscription, ExecutableFromACampaignAction, IsSearchable
{
    use AuditableTrait;
    use Searchable;

    protected $fillable = [
        'user_id',
        'interactable_id',
        'interactable_type',
        'interaction_campaign_id',
        'interaction_driver_id',
        'division_id',
        'interaction_outcome_id',
        'interaction_relation_id',
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

    public function searchableAs(): string
    {
        return config('scout.prefix') . 'interactions';
    }

    public function toSearchableArray(): array
    {
        return [
            'id' => (int) $this->getScoutKey(),
            'subject' => $this->subject,
            'description' => $this->description,
            'user_id' => $this->user_id,
            'interactable_id' => $this->interactable_id,
            'interactable_type' => $this->interactable_type,
            'interaction_type_id' => $this->interaction_type_id,
            'interaction_relation_id' => $this->interaction_relation_id,
            'interaction_campaign_id' => $this->interaction_campaign_id,
            'interaction_driver_id' => $this->interaction_driver_id,
            'interaction_status_id' => $this->interaction_status_id,
            'interaction_outcome_id' => $this->interaction_outcome_id,
            'division_id' => $this->division_id,
            'start_datetime' => $this->created_at->format('Y-m-d H:i:s'),
            'end_datetime' => $this->created_at->format('Y-m-d H:i:s'),
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at->format('Y-m-d H:i:s'),
        ];
    }

    public function getWebPermissions(): Collection
    {
        return collect(['import', ...$this->webPermissions()]);
    }

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

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(InteractionCampaign::class, 'interaction_campaign_id');
    }

    public function driver(): BelongsTo
    {
        return $this->belongsTo(InteractionDriver::class, 'interaction_driver_id');
    }

    public function division(): BelongsTo
    {
        return $this->belongsTo(Division::class, 'division_id');
    }

    public function outcome(): BelongsTo
    {
        return $this->belongsTo(InteractionOutcome::class, 'interaction_outcome_id');
    }

    public function relation(): BelongsTo
    {
        return $this->belongsTo(InteractionRelation::class, 'interaction_relation_id');
    }

    public function status(): BelongsTo
    {
        return $this->belongsTo(InteractionStatus::class, 'interaction_status_id');
    }

    public function type(): BelongsTo
    {
        return $this->belongsTo(InteractionType::class, 'interaction_type_id');
    }

    public static function executeFromCampaignAction(CampaignAction $action): bool|string
    {
        try {
            $action->campaign->caseload->retrieveRecords()->each(function (Educatable $educatable) use ($action) {
                Interaction::create([
                    'user_id' => $action->campaign->user_id,
                    'interactable_type' => $educatable->getMorphClass(),
                    'interactable_id' => $educatable->getKey(),
                    'interaction_type_id' => $action->data['interaction_type_id'],
                    'interaction_relation_id' => $action->data['interaction_relation_id'],
                    'interaction_campaign_id' => $action->data['interaction_campaign_id'],
                    'interaction_driver_id' => $action->data['interaction_driver_id'],
                    'interaction_status_id' => $action->data['interaction_status_id'],
                    'interaction_outcome_id' => $action->data['interaction_outcome_id'],
                    'division_id' => $action->data['division_id'],
                ]);
            });

            return true;
        } catch (Exception $e) {
            return $e->getMessage();
        }

        // Do we need to be able to relate campaigns/actions to the RESULT of their actions?
    }
}
