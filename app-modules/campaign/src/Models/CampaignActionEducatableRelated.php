<?php

namespace AdvisingApp\Campaign\Models;

use Illuminate\Database\Eloquent\Concerns\HasVersion4Uuids as HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class CampaignActionEducatableRelated extends Model
{
    use HasUuids;

    protected $table = 'campaign_action_educatable_related';

    /**
     * @return BelongsTo<CampaignActionEducatable, $this>
     */
    public function campaignActionEducatable(): BelongsTo
    {
        return $this->belongsTo(CampaignActionEducatable::class, 'campaign_action_educatable_id');
    }

    /**
     * @return MorphTo<Model, $this>
     */
    public function related(): MorphTo
    {
        return $this->morphTo(
            'related',
            'related_type',
            'related_id',
            'id'
        );
    }
}
