<?php

/*
<COPYRIGHT>

    Copyright © 2016-2025, Canyon GBS LLC. All rights reserved.

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

use AdvisingApp\Ai\Enums\AiModel;
use AdvisingApp\Ai\Models\Concerns\CanAddAssistantLicenseGlobalScope;
use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Auditable as AuditableTrait;
use OwenIt\Auditing\Contracts\Auditable;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\File;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

/**
 * @mixin IdeHelperQnaAdvisor
 */
class QnaAdvisor extends BaseModel implements HasMedia, Auditable
{
    use CanAddAssistantLicenseGlobalScope;
    use InteractsWithMedia;
    use SoftDeletes;
    use AuditableTrait;

    protected $fillable = [
        'archived_at',
        'name',
        'model',
        'description',
        'is_embed_enabled',
        'authorized_domains',
        'is_requires_authentication_enabled',
    ];

    protected $casts = [
        'archived_at' => 'datetime',
        'model' => AiModel::class,
        'is_embed_enabled' => 'boolean',
        'authorized_domains' => 'json',
        'is_requires_authentication_enabled' => 'boolean',
    ];

    /**
     * @return HasMany<QnaAdvisorCategory, $this>
     */
    public function categories(): HasMany
    {
        return $this->hasMany(QnaAdvisorCategory::class, 'qna_advisor_id');
    }

    /**
     * @return HasManyThrough<QnaAdvisorQuestion, QnaAdvisorCategory, $this>
     */
    public function questions(): HasManyThrough
    {
        return $this->hasManyThrough(
            QnaAdvisorQuestion::class,
            QnaAdvisorCategory::class,
            'qna_advisor_id',
            'category_id',
            'id',
            'id'
        );
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('avatar')
            ->acceptsFile(function (File $file) {
                return in_array($file->mimeType, [
                    'image/png',
                    'image/jpeg',
                    'image/gif',
                ]);
            });
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('avatar-height-250px')
            ->performOnCollections('avatar')
            ->height(250);

        $this->addMediaConversion('thumbnail')
            ->performOnCollections('avatar')
            ->width(32)
            ->height(32);
    }

    /**
     * @return HasMany<QnaAdvisorFile, $this>
     */
    public function files(): HasMany
    {
        return $this->hasMany(QnaAdvisorFile::class, 'advisor_id');
    }

    /**
     * @return HasMany<QnaAdvisorLink, $this>
     */
    public function links(): HasMany
    {
        return $this->hasMany(QnaAdvisorLink::class, 'advisor_id');
    }

    public function getInstructionsCacheKey(): string
    {
        return 'qna-advisor-' . $this->getKey() . '-instructions';
    }

    /**
     * @return HasMany<QnaAdvisorThread, $this>
     */
    public function threads(): HasMany
    {
        return $this->hasMany(QnaAdvisorThread::class, 'advisor_id');
    }
}
