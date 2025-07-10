<?php

namespace AdvisingApp\Research\Models;

use AdvisingApp\Research\Database\Factories\ResearchRequestParsedFileFactory;
use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

/**
 * @mixin IdeHelperResearchRequestParsedFile
 */
class ResearchRequestParsedFile extends BaseModel
{
    /** @use HasFactory<ResearchRequestParsedFileFactory> */
    use HasFactory;

    use SoftDeletes;

    public $fillable = [
        'research_request_id',
        'uploaded_at',
        'results',
        'media_id',
        'file_id',
    ];

    /**
     * @return BelongsTo<Media, $this>
     */
    public function media(): BelongsTo
    {
        return $this->belongsTo(Media::class);
    }

    /**
     * @return BelongsTo<ResearchRequest, $this>
     */
    public function researchRequest(): BelongsTo
    {
        return $this->belongsTo(ResearchRequest::class);
    }
}
