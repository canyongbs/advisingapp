<?php

namespace AdvisingApp\Research\Models;

use AdvisingApp\Research\Database\Factories\ResearchRequestParsedSearchResultsFactory;
use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @mixin IdeHelperResearchRequestParsedSearchResults
 */
class ResearchRequestParsedSearchResults extends BaseModel
{
    /** @use HasFactory<ResearchRequestParsedSearchResultsFactory> */
    use HasFactory;

    use SoftDeletes;

    public $fillable = [
        'research_request_id',
        'results',
        'search_query',
    ];

    /**
     * @return BelongsTo<ResearchRequest, $this>
     */
    public function researchRequest(): BelongsTo
    {
        return $this->belongsTo(ResearchRequest::class);
    }
}
