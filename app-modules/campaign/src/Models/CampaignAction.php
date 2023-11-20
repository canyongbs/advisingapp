<?php

namespace Assist\Campaign\Models;

use App\Models\BaseModel;
use OwenIt\Auditing\Contracts\Auditable;
use Assist\Campaign\Enums\CampaignActionType;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
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
