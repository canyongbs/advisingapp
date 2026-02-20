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
use Exception;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Auditable as AuditableTrait;
use OwenIt\Auditing\Contracts\Auditable;

/**
 * @mixin IdeHelperQnaAdvisorLink
 */
class QnaAdvisorLink extends BaseModel implements AiFile, Auditable
{
    use SoftDeletes;
    use AuditableTrait;

    protected $fillable = [
        'url',
        'advisor_id',
        'parsing_results',
        'is_current',
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
