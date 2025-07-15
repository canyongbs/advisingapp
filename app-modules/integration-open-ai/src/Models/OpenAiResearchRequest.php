<?php

namespace AdvisingApp\IntegrationOpenAi\Models;

use AdvisingApp\Research\Models\ResearchRequest;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class OpenAiResearchRequest extends Model
{
    use SoftDeletes;

    public $fillable = [
        'research_request_id',
        'deployment_hash',
        'vector_store_ready_until',
        'vector_store_id',
        'outline_response_id',
    ];

    protected $casts = [
        'vector_store_ready_until' => 'immutable_datetime',
    ];

    /**
     * @return BelongsTo<ResearchRequest, $this>
     */
    public function researchRequest(): BelongsTo
    {
        return $this->belongsTo(ResearchRequest::class);
    }
}
