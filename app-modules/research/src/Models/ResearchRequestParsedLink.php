<?php

namespace AdvisingApp\Research\Models;

use AdvisingApp\Research\Database\Factories\ResearchRequestParsedLinkFactory;
use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @mixin IdeHelperResearchRequestParsedLink
 */
class ResearchRequestParsedLink extends BaseModel
{
    /** @use HasFactory<ResearchRequestParsedLinkFactory> */
    use HasFactory;

    use SoftDeletes;

    public $fillable = [
        'research_request_id',
        'results',
        'url',
    ];

    /**
     * @return BelongsTo<ResearchRequest, $this>
     */
    public function researchRequest(): BelongsTo
    {
        return $this->belongsTo(ResearchRequest::class);
    }
}
