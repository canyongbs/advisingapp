<?php

namespace Assist\KnowledgeBase\Models;

use Eloquent;
use DateTimeInterface;
use App\Models\BaseModel;
use Illuminate\Support\Carbon;
use OwenIt\Auditing\Models\Audit;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Assist\Audit\Models\Concerns\Auditable as AuditableTrait;
use Assist\KnowledgeBase\Database\Factories\KnowledgeBaseStatusFactory;

/**
 * Assist\KnowledgeBase\Models\KnowledgeBaseStatus
 *
 * @property string $id
 * @property string $name
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property-read Collection<int, Audit> $audits
 * @property-read int|null $audits_count
 * @property-read Collection<int, KnowledgeBaseItem> $knowledgeBaseItems
 * @property-read int|null $knowledge_base_items_count
 *
 * @method static KnowledgeBaseStatusFactory factory($count = null, $state = [])
 * @method static Builder|KnowledgeBaseStatus newModelQuery()
 * @method static Builder|KnowledgeBaseStatus newQuery()
 * @method static Builder|KnowledgeBaseStatus onlyTrashed()
 * @method static Builder|KnowledgeBaseStatus query()
 * @method static Builder|KnowledgeBaseStatus whereCreatedAt($value)
 * @method static Builder|KnowledgeBaseStatus whereDeletedAt($value)
 * @method static Builder|KnowledgeBaseStatus whereId($value)
 * @method static Builder|KnowledgeBaseStatus whereName($value)
 * @method static Builder|KnowledgeBaseStatus whereUpdatedAt($value)
 * @method static Builder|KnowledgeBaseStatus withTrashed()
 * @method static Builder|KnowledgeBaseStatus withoutTrashed()
 *
 * @mixin Eloquent
 */
class KnowledgeBaseStatus extends BaseModel implements Auditable
{
    use SoftDeletes;
    use AuditableTrait;
    use HasUuids;

    protected $fillable = [
        'name',
    ];

    public function knowledgeBaseItems(): HasMany
    {
        return $this->hasMany(KnowledgeBaseItem::class, 'status_id');
    }

    protected function serializeDate(DateTimeInterface $date): string
    {
        return $date->format(config('project.datetime_format') ?? 'Y-m-d H:i:s');
    }
}
