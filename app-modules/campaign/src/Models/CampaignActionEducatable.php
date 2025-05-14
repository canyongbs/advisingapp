<?php

namespace AdvisingApp\Campaign\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;

class CampaignActionEducatable extends Model
{
    use HasUuids;
    use UsesTenantConnection;

    protected $fillable = [
        'succeeded_at',
        'last_failed_at',
    ];

    protected $casts = [
        'succeeded_at' => 'datetime',
        'last_failed_at' => 'datetime',
    ];

    /**
     * @return BelongsTo<CampaignAction, $this>
     */
    public function campaignAction(): BelongsTo
    {
        return $this->belongsTo(CampaignAction::class);
    }

    /**
     * @return MorphTo<Model, $this>
     */
    public function educatable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * @return MorphTo<Model, $this>
     */
    public function related(): MorphTo
    {
        return $this->morphTo();
    }
}
