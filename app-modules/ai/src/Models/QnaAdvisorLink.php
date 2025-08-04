<?php

namespace AdvisingApp\Ai\Models;

use AdvisingApp\Ai\Models\Contracts\AiFile;
use AdvisingApp\IntegrationOpenAi\Models\OpenAiVectorStore;
use App\Models\BaseModel;
use Exception;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class QnaAdvisorLink extends BaseModel implements AiFile
{
    use SoftDeletes;

    protected $fillable = [
        'url',
        'advisor_id',
        'parsing_results',
    ];

    /**
     * @return BelongsTo<QnaAdvisor, $this>
     */
    public function advisor(): BelongsTo
    {
        return $this->belongsTo(QnaAdvisor::class, 'advisor_id');
    }

    public function getKey(): string
    {
        return parent::getKey();
    }

    public function getTemporaryUrl(): ?string
    {
        throw new Exception('Temporary URL is not applicable for links.');
    }

    public function getName(): ?string
    {
        return $this->url;
    }

    public function getMimeType(): ?string
    {
        return 'text/markdown';
    }

    public function getFileId(): ?string
    {
        throw new Exception('Links do not have a file ID, as they are not parsed by LlamaParse.');
    }

    public function getParsingResults(): ?string
    {
        return $this->parsing_results;
    }

    /**
     * @return MorphOne<OpenAiVectorStore, $this>
     */
    public function openAiVectorStore(): MorphOne
    {
        return $this->morphOne(OpenAiVectorStore::class, 'file');
    }
}
