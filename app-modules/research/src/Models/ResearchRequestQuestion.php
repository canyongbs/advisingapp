<?php

namespace AdvisingApp\Research\Models;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Database\Factories\ResearchRequestQuestionFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ResearchRequestQuestion extends BaseModel
{
    /** @use HasFactory<ResearchRequestQuestionFactory> */
    use HasFactory;

    protected $fillable = [
        'content',
        'response',
        'research_request_id',
    ];

    /**
     * @return BelongsTo<ResearchRequest, $this>
     */
    public function researchRequest(): BelongsTo
    {
        return $this->belongsTo(ResearchRequest::class);
    }
}
