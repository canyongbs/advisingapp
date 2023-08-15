<?php

namespace Assist\KnowledgeBase\Models;

use Eloquent;
use DateTimeInterface;
use App\Models\BaseModel;
use Illuminate\Support\Carbon;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Auditable as AuditableTrait;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Assist\KnowledgeBase\Database\Factories\KnowledgeBaseCategoryFactory;

/**
 * Assist\KnowledgeBase\Models\KnowledgeBaseCategory
 *
 * @property int $id
 * @property string $name
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property-read Collection<int, KnowledgeBaseItem> $knowledgeBaseItems
 * @property-read int|null $knowledge_base_items_count
 *
 * @method static KnowledgeBaseCategoryFactory factory($count = null, $state = [])
 * @method static Builder|KnowledgeBaseCategory newModelQuery()
 * @method static Builder|KnowledgeBaseCategory newQuery()
 * @method static Builder|KnowledgeBaseCategory onlyTrashed()
 * @method static Builder|KnowledgeBaseCategory query()
 * @method static Builder|KnowledgeBaseCategory whereCreatedAt($value)
 * @method static Builder|KnowledgeBaseCategory whereDeletedAt($value)
 * @method static Builder|KnowledgeBaseCategory whereId($value)
 * @method static Builder|KnowledgeBaseCategory whereName($value)
 * @method static Builder|KnowledgeBaseCategory whereUpdatedAt($value)
 * @method static Builder|KnowledgeBaseCategory withTrashed()
 * @method static Builder|KnowledgeBaseCategory withoutTrashed()
 *
 * @mixin Eloquent
 */
class KnowledgeBaseCategory extends BaseModel implements Auditable
{
    use SoftDeletes;
    use AuditableTrait;
    use HasUuids;

    protected $fillable = [
        'name',
    ];

    public function knowledgeBaseItems(): HasMany
    {
        return $this->hasMany(KnowledgeBaseItem::class, 'category_id');
    }

    protected function serializeDate(DateTimeInterface $date): string
    {
        return $date->format(config('project.datetime_format') ?? 'Y-m-d H:i:s');
    }
}
