<?php

/*
<COPYRIGHT>

    Copyright © 2016-2024, Canyon GBS LLC. All rights reserved.

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

use App\Models\BaseModel;
use AdvisingApp\Ai\Enums\AiModel;
use Spatie\MediaLibrary\HasMedia;
use AdvisingApp\Ai\Enums\AiApplication;
use OwenIt\Auditing\Contracts\Auditable;
use Spatie\MediaLibrary\InteractsWithMedia;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\MediaCollections\File;
use OwenIt\Auditing\Auditable as AuditableTrait;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use AdvisingApp\Ai\Models\Concerns\CanAddAssistantLicenseGlobalScope;
use AdvisingApp\Ai\Exceptions\DefaultAssistantLockedPropertyException;

/**
 * @mixin IdeHelperAiAssistant
 */
class AiAssistant extends BaseModel implements HasMedia, Auditable
{
    use CanAddAssistantLicenseGlobalScope;
    use InteractsWithMedia;
    use SoftDeletes;
    use AuditableTrait;

    protected $fillable = [
        'archived_at',
        'assistant_id',
        'name',
        'application',
        'model',
        'is_default',
        'description',
        'instructions',
        'knowledge',
    ];

    protected $casts = [
        'application' => AiApplication::class,
        'archived_at' => 'datetime',
        'is_default' => 'bool',
        'model' => AiModel::class,
    ];

    protected ?bool $isUpvoted = null;

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('avatar')
            ->acceptsFile(function (File $file) {
                if ($this->application === AiApplication::PersonalAssistant && $this->is_default) {
                    throw new DefaultAssistantLockedPropertyException('avatar');
                }

                return in_array($file->mimeType, [
                    'image/png',
                    'image/jpeg',
                    'image/gif',
                ]);
            });
    }

    public function registerMediaConversions(Media $media = null): void
    {
        $this->addMediaConversion('avatar-height-250px')
            ->performOnCollections('avatar')
            ->height(250);

        $this->addMediaConversion('thumbnail')
            ->performOnCollections('avatar')
            ->width(32)
            ->height(32);
    }

    public function threads(): HasMany
    {
        return $this->hasMany(AiThread::class, 'assistant_id');
    }

    public function files(): HasMany
    {
        return $this->hasMany(AiAssistantFile::class, 'assistant_id');
    }

    public function upvotes(): HasMany
    {
        return $this->hasMany(AiAssistantUpvote::class, 'assistant_id');
    }

    public function isUpvoted(): bool
    {
        return $this->isUpvoted ??= $this->upvotes()->whereBelongsTo(auth()->user())->exists();
    }

    public function upvote(): void
    {
        $this->upvotes()->create(['user_id' => auth()->id()]);

        $this->isUpvoted = true;
    }

    public function cancelUpvote(): void
    {
        $this->upvotes()->whereBelongsTo(auth()->user())->delete();

        $this->isUpvoted = false;
    }

    public function toggleUpvote(): void
    {
        if ($this->isUpvoted()) {
            $this->cancelUpvote();

            return;
        }

        $this->upvote();
    }

    public function isDefault(): bool
    {
        return $this->is_default ?? false;
    }
}
