<?php

namespace AdvisingApp\Campaign\Models;

use AdvisingApp\Campaign\Database\Factories\CampaignActionEducatableFactory;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;

/**
 * @mixin IdeHelperCampaignActionEducatable
 */
class CampaignActionEducatable extends Model
{
    use HasUuids;
    use UsesTenantConnection;

    /** @use HasFactory<CampaignActionEducatableFactory> */
    use HasFactory;

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

    public function markSucceeded(?Carbon $at = null): void
    {
        $this->update([
            'succeeded_at' => $at ?? now(),
        ]);
    }

    public function markFailed(?Carbon $at = null): void
    {
        $this->update([
            'last_failed_at' => $at ?? now(),
        ]);
    }
}
