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

use AdvisingApp\Ai\Enums\AiAssistantApplication;
use AdvisingApp\Ai\Enums\AiModel;
use AdvisingApp\Ai\Exceptions\DefaultAssistantLockedPropertyException;
use AdvisingApp\Ai\Models\Concerns\CanAddAssistantLicenseGlobalScope;
use AdvisingApp\Ai\Models\Contracts\AiFile;
use AdvisingApp\Ai\Models\Scopes\AiAssistantConfidentialScope;
use AdvisingApp\Ai\Observers\AiAssistantObserver;
use AdvisingApp\ResourceHub\Models\ResourceHubArticle;
use AdvisingApp\Team\Models\Team;
use App\Features\ResourceHubKnowledgeFeature;
use App\Models\BaseModel;
use App\Models\User;
use CanyonGBS\Common\Models\Concerns\HasUserSaveTracking;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Auditable as AuditableTrait;
use OwenIt\Auditing\Contracts\Auditable;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\File;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

/**
 * @mixin IdeHelperAiAssistant
 */
#[ObservedBy([AiAssistantObserver::class])] #[ScopedBy(AiAssistantConfidentialScope::class)]
class AiAssistant extends BaseModel implements HasMedia, Auditable
{
    use CanAddAssistantLicenseGlobalScope;
    use HasUserSaveTracking;
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
        'is_confidential',
        'has_resource_hub_knowledge',
        'created_by_id',
        'last_updated_by_id',
    ];

    protected $casts = [
        'application' => AiAssistantApplication::class,
        'archived_at' => 'datetime',
        'is_default' => 'bool',
        'model' => AiModel::class,
        'is_confidential' => 'bool',
        'has_resource_hub_knowledge' => 'bool',
    ];

    protected ?bool $isUpvoted = null;

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('avatar')
            ->acceptsFile(function (File $file) {
                if ($this->application === AiAssistantApplication::PersonalAssistant && $this->is_default) {
                    throw new DefaultAssistantLockedPropertyException('avatar');
                }

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
     * @return HasMany<AiThread, $this>
     */
    public function threads(): HasMany
    {
        return $this->hasMany(AiThread::class, 'assistant_id');
    }

    /**
     * @return HasMany<AiAssistantFile, $this>
     */
    public function files(): HasMany
    {
        return $this->hasMany(AiAssistantFile::class, 'assistant_id');
    }

    /**
     * @return HasMany<AiAssistantUpvote, $this>
     */
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

    /**
     * @return BelongsToMany<User, $this, covariant AiAssistantConfidentialUser>
     */
    public function confidentialAccessUsers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'ai_assistant_confidential_users')
            ->using(AiAssistantConfidentialUser::class)
            ->withTimestamps();
    }

    /**
     * @return BelongsToMany<Team, $this, covariant AiAssistantConfidentialTeam>
     */
    public function confidentialAccessTeams(): BelongsToMany
    {
        return $this->belongsToMany(Team::class, 'ai_assistant_confidential_teams')
            ->using(AiAssistantConfidentialTeam::class)
            ->withTimestamps();
    }

    /**
     * @return HasMany<AiAssistantUse, $this>
     */
    public function uses(): HasMany
    {
        return $this->hasMany(AiAssistantUse::class, 'assistant_id');
    }

    /**
     * @return HasMany<AiAssistantLink, $this>
     */
    public function links(): HasMany
    {
        return $this->hasMany(AiAssistantLink::class, 'ai_assistant_id');
    }

    /**
     * @return array<AiFile>
     */
    public function getResourceHubArticles(): array
    {
        if (! ResourceHubKnowledgeFeature::active()) {
            return [];
        }

        if (! $this->has_resource_hub_knowledge) {
            return [];
        }

        return ResourceHubArticle::query()
            ->public()
            ->whereNotNull('article_details')
            ->get()
            ->all();
    }
}
