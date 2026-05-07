<?php

namespace AdvisingApp\Ai\Models;

use AdvisingApp\ResourceHub\Models\ResourceHubCategory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

class AiAssistantResourceHubCategory extends Pivot
{
    use HasUuids;

    protected array $fillable = [
        'ai_assistant_id',
        'resource_hub_category_id',
    ];

    /**
     * @return BelongsTo<AiAssistant, $this>
     */
    public function aiAssistant(): BelongsTo
    {
        return $this->belongsTo(AiAssistant::class);
    }

    /**
     * @return BelongsTo<ResourceHubCategory, $this>
     */
    public function resourceHubCategory(): BelongsTo
    {
        return $this->belongsTo(ResourceHubCategory::class);
    }
}
