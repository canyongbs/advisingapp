<?php

namespace Assist\Campaign\Models;

use App\Models\BaseModel;
use OwenIt\Auditing\Contracts\Auditable;
use Assist\Campaign\Enums\CampaignActionType;
use Assist\Engagement\Models\EngagementBatch;
use Illuminate\Database\Eloquent\SoftDeletes;
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
        'executed_at',
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
        match ($this->type) {
            CampaignActionType::BulkEngagement => EngagementBatch::executeFromCampaignAction($this),
            default => null
        };

        $this->markAsExecuted();
    }

    public function markAsExecuted(): void
    {
        $this->update([
            'executed_at' => now(),
        ]);
    }

    public function hasBeenExecuted(): bool
    {
        return (bool) ! is_null($this->executed_at);
    }

    public function getEditFields(): array
    {
        return match ($this->type) {
            CampaignActionType::BulkEngagement => EngagementBatch::getEditFormFields(),
            default => []
        };
    }
}
