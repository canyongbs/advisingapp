<?php

namespace AdvisingApp\IntegrationOpenAi\Models;

use AdvisingApp\Research\Models\ResearchRequest;
use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @mixin IdeHelperOpenAiResearchRequestVectorStore
 */
class OpenAiResearchRequestVectorStore extends BaseModel
{
    use SoftDeletes;

    public $fillable = [
        'research_request_id',
        'deployment_hash',
        'ready_until',
        'vector_store_id',
    ];

    protected $casts = [
        'ready_until' => 'immutable_datetime',
    ];

    /**
     * @return BelongsTo<ResearchRequest, $this>
     */
    public function researchRequest(): BelongsTo
    {
        return $this->belongsTo(ResearchRequest::class);
    }
}
