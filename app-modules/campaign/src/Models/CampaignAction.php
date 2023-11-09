<?php

namespace Assist\Campaign\Models;

use App\Models\BaseModel;
use Assist\Task\Models\Task;
use Assist\Alert\Models\Alert;
use Assist\CareTeam\Models\CareTeam;
use OwenIt\Auditing\Contracts\Auditable;
use Assist\Interaction\Models\Interaction;
use Assist\Campaign\Enums\CampaignActionType;
use Assist\Engagement\Models\EngagementBatch;
use Illuminate\Database\Eloquent\SoftDeletes;
use Assist\Campaign\Filament\Blocks\TaskBlock;
use Assist\Campaign\Filament\Blocks\CareTeamBlock;
use Assist\ServiceManagement\Models\ServiceRequest;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Assist\Campaign\Filament\Blocks\InteractionBlock;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Assist\Campaign\Filament\Blocks\ProactiveAlertBlock;
use Assist\Campaign\Filament\Blocks\ServiceRequestBlock;
use Assist\Campaign\Filament\Blocks\EngagementBatchBlock;
use Assist\Audit\Models\Concerns\Auditable as AuditableTrait;

/**
 * @mixin IdeHelperCampaignAction
 */
class CampaignAction extends BaseModel implements Auditable
{
    use AuditableTrait;
    use SoftDeletes;

    protected $fillable = [
        'campaign_id',
        'type',
        'data',
        'execute_at',
        'last_execution_attempt_at',
        'last_execution_attempt_error',
        'successfully_executed_at',
    ];

    protected $casts = [
        'type' => CampaignActionType::class,
        'data' => 'array',
    ];

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(Campaign::class);
    }

    public function execute(): void
    {
        $response = match ($this->type) {
            CampaignActionType::BulkEngagement => EngagementBatch::executeFromCampaignAction($this),
            CampaignActionType::ServiceRequest => ServiceRequest::executeFromCampaignAction($this),
            CampaignActionType::ProactiveAlert => Alert::executeFromCampaignAction($this),
            CampaignActionType::Interaction => Interaction::executeFromCampaignAction($this),
            CampaignActionType::CareTeam => CareTeam::executeFromCampaignAction($this),
            CampaignActionType::Task => Task::executeFromCampaignAction($this),
            default => null
        };

        $response === true ? $this->markAsSuccessfullyExecuted() : $this->markAsUnsuccessfullyExecuted($response);
    }

    public function markAsSuccessfullyExecuted(): void
    {
        $this->update([
            'last_execution_attempt_at' => now(),
            'successfully_executed_at' => now(),
        ]);
    }

    public function markAsUnsuccessfullyExecuted(string $response): void
    {
        $this->update([
            'last_execution_attempt_at' => now(),
            'last_execution_attempt_error' => $response,
        ]);
    }

    public function scopeHasNotBeenExecuted(Builder $query): void
    {
        $query->whereNull('successfully_executed_at');
    }

    public function scopeCampaignEnabled(Builder $query): void
    {
        $query->whereRelation('campaign', 'enabled', true);
    }

    public function hasBeenExecuted(): bool
    {
        return (bool) ! is_null($this->successfully_executed_at);
    }

    public function getEditFields(): array
    {
        return match ($this->type) {
            CampaignActionType::BulkEngagement => EngagementBatchBlock::make()->editFields(),
            CampaignActionType::ServiceRequest => ServiceRequestBlock::make()->editFields(),
            CampaignActionType::ProactiveAlert => ProactiveAlertBlock::make()->editFields(),
            CampaignActionType::Interaction => InteractionBlock::make()->editFields(),
            CampaignActionType::CareTeam => CareTeamBlock::make()->editFields(),
            CampaignActionType::Task => TaskBlock::make()->editFields(),
            default => []
        };
    }
}
