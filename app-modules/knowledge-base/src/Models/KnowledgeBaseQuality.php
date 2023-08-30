<?php

namespace Assist\KnowledgeBase\Models;

use Eloquent;
use DateTimeInterface;
use App\Models\BaseModel;
use Assist\Audit\Models\Audit;
use Illuminate\Support\Carbon;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Assist\Audit\Models\Concerns\Auditable as AuditableTrait;

/**
 * Assist\KnowledgeBase\Models\KnowledgeBaseQuality
 *
 * @property string $id
 * @property string $name
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property-read Collection<int, Audit> $audits
 * @property-read int|null $audits_count
 * @property-read Collection<int, \Assist\KnowledgeBase\Models\KnowledgeBaseItem> $knowledgeBaseItems
 * @property-read int|null $knowledge_base_items_count
 *
 * @method static \Assist\KnowledgeBase\Database\Factories\KnowledgeBaseQualityFactory factory($count = null, $state = [])
 * @method static Builder|KnowledgeBaseQuality newModelQuery()
 * @method static Builder|KnowledgeBaseQuality newQuery()
 * @method static Builder|KnowledgeBaseQuality onlyTrashed()
 * @method static Builder|KnowledgeBaseQuality query()
 * @method static Builder|KnowledgeBaseQuality whereCreatedAt($value)
 * @method static Builder|KnowledgeBaseQuality whereDeletedAt($value)
 * @method static Builder|KnowledgeBaseQuality whereId($value)
 * @method static Builder|KnowledgeBaseQuality whereName($value)
 * @method static Builder|KnowledgeBaseQuality whereUpdatedAt($value)
 * @method static Builder|KnowledgeBaseQuality withTrashed()
 * @method static Builder|KnowledgeBaseQuality withoutTrashed()
 *
 * @mixin Eloquent
 */
class KnowledgeBaseQuality extends BaseModel implements Auditable
{
    use SoftDeletes;
    use AuditableTrait;
    use HasUuids;

    protected $fillable = [
        'name',
    ];

    public function knowledgeBaseItems(): HasMany
    {
        return $this->hasMany(KnowledgeBaseItem::class, 'quality_id');
    }

    protected function serializeDate(DateTimeInterface $date): string
    {
        return $date->format(config('project.datetime_format') ?? 'Y-m-d H:i:s');
    }
}
