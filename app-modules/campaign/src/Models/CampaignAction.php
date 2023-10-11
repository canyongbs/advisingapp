<?php

namespace Assist\Campaign\Models;

use App\Models\BaseModel;
use OwenIt\Auditing\Contracts\Auditable;
use Assist\Engagement\Models\EngagementBatch;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Modules\Campaign\Enums\CampaignActionType;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Assist\Audit\Models\Concerns\Auditable as AuditableTrait;

class CampaignAction extends BaseModel implements Auditable
{
    use AuditableTrait;
    use SoftDeletes;

    protected $fillable = [
        'type',
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
    }
}
