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

namespace AdvisingApp\ResourceHub\Models;

use DateTimeInterface;
use App\Models\BaseModel;
use Spatie\MediaLibrary\HasMedia;
use OwenIt\Auditing\Contracts\Auditable;
use AdvisingApp\Division\Models\Division;
use Spatie\MediaLibrary\InteractsWithMedia;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use AdvisingApp\Audit\Models\Concerns\Auditable as AuditableTrait;

/**
 * @mixin IdeHelperResourceHubArticle
 */
class ResourceHubArticle extends BaseModel implements Auditable, HasMedia
{
    use AuditableTrait;
    use HasUuids;
    use InteractsWithMedia;
    use SoftDeletes;

    protected $table = 'knowledge_base_articles';

    protected $casts = [
        'public' => 'boolean',
        'article_details' => 'array',
    ];

    protected $fillable = [
        'article_details',
        'category_id',
        'notes',
        'public',
        'quality_id',
        'status_id',
        'title',
    ];

    protected ?bool $isUpvoted = null;

    public function quality(): BelongsTo
    {
        return $this->belongsTo(ResourceHubQuality::class);
    }

    public function status(): BelongsTo
    {
        return $this->belongsTo(ResourceHubStatus::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(ResourceHubCategory::class);
    }

    public function division(): BelongsToMany
    {
        return $this->belongsToMany(Division::class, 'division_knowledge_base_item', 'knowledge_base_item_id', 'division_id');
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('solution');
        $this->addMediaCollection('notes');
    }

    public function scopePublic($query)
    {
        return $query->where('public', true);
    }

    public function views(): HasMany
    {
        return $this->hasMany(ResourceHubArticleView::class, 'knowledge_base_item_id');
    }

    public function upvotes(): HasMany
    {
        return $this->hasMany(ResourceHubArticleUpvote::class, 'knowledge_base_item_id');
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

    protected function serializeDate(DateTimeInterface $date): string
    {
        return $date->format(config('project.datetime_format') ?? 'Y-m-d H:i:s');
    }
}
