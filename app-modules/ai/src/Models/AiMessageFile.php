<?php

/*
<COPYRIGHT>

    Copyright © 2016-2026, Canyon GBS LLC. All rights reserved.

    Advising App™ is licensed under the Elastic License 2.0. For more details,
    see https://github.com/canyongbs/advisingapp/blob/main/LICENSE.

    Notice:

    - You may not provide the software to third parties as a hosted or managed
      service, where the service provides users with access to any substantial set of
      the features or functionality of the software.
    - You may not move, change, disable, or circumvent the license key functionality
      in the software, and you may not remove or obscure any functionality in the
      software that is protected by the license key.
    - You may not alter, remove, or obscure any licensing, copyright, or other notices
      of the licensor in the software. Any use of the licensor’s trademarks is subject
      to applicable law.
    - Canyon GBS LLC respects the intellectual property rights of others and expects the
      same in return. Canyon GBS™ and Advising App™ are registered trademarks of
      Canyon GBS LLC, and we are committed to enforcing and protecting our trademarks
      vigorously.
    - The software solution, including services, infrastructure, and code, is offered as a
      Software as a Service (SaaS) by Canyon GBS LLC.
    - Use of this software implies agreement to the license terms and conditions as stated
      in the Elastic License 2.0.

    For more information or inquiries please visit our website at
    https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

namespace AdvisingApp\Ai\Models;

use AdvisingApp\Ai\Models\Contracts\AiFile;
use AdvisingApp\IntegrationOpenAi\Models\OpenAiVectorStore;
use App\Models\BaseModel;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Prunable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

/**
 * @mixin IdeHelperAiMessageFile
 */
class AiMessageFile extends BaseModel implements AiFile, HasMedia
{
    use SoftDeletes;
    use InteractsWithMedia;
    use Prunable;

    protected $fillable = [
        'file_id',
        'message_id',
        'mime_type',
        'name',
        'temporary_url',
        'parsing_results',
    ];

    /**
     * @return BelongsTo<AiMessage, $this>
     */
    public function message(): BelongsTo
    {
        return $this->belongsTo(AiMessage::class, 'message_id');
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('files')
            ->singleFile();
    }

    public function prunable(): Builder
    {
        return static::query()
            ->whereNotNull('deleted_at')
            ->where('deleted_at', '<=', now()->subDays(7));
    }

    public function getKey(): string
    {
        return parent::getKey();
    }

    public function getTemporaryUrl(): ?string
    {
        return $this->temporary_url;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function getMimeType(): ?string
    {
        return $this->mime_type;
    }

    public function getFileId(): ?string
    {
        return $this->file_id;
    }

    public function getParsingResults(): ?string
    {
        return $this->parsing_results;
    }

    /**
     * @return MorphMany<OpenAiVectorStore, $this>
     */
    public function openAiVectorStores(): MorphMany
    {
        return $this->morphMany(OpenAiVectorStore::class, 'file');
    }
}
