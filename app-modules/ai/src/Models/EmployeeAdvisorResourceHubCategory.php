<?php

namespace AdvisingApp\Ai\Models;

use AdvisingApp\ResourceHub\Models\ResourceHubCategory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

class EmployeeAdvisorResourceHubCategory extends Pivot
{
    use HasUuids;

    /**
     * @return BelongsTo<AiAssistant, $this>
     */
    public function aiAssistant(): BelongsTo
    {
        return $this->belongsTo(AiAssistant::class, 'employee_advisor_id');
    }

    /**
     * @return BelongsTo<ResourceHubCategory, $this>
     */
    public function resourceHubCategory(): BelongsTo
    {
        return $this->belongsTo(ResourceHubCategory::class);
    }
}
