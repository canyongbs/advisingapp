<?php

namespace Assist\Campaign\Models;

use App\Models\BaseModel;
use Assist\Campaign\Filament\Blocks\SubscriptionBlock;
use Assist\Notifications\Models\Subscription;
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
        $response = $this->type->executeAction($this);

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
        return ! is_null($this->successfully_executed_at);
    }
}
