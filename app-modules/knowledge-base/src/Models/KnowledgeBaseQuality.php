<?php

namespace Assist\KnowledgeBase\Models;

use Eloquent;
use DateTimeInterface;
use App\Models\BaseModel;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;
use Assist\KnowledgeBase\Database\Factories\KnowledgeBaseQualityFactory;

/**
 * Assist\KnowledgeBase\Models\KnowledgeBaseQuality
 *
 * @property int $id
 * @property string $name
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 *
 * @method static KnowledgeBaseQualityFactory factory($count = null, $state = [])
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
class KnowledgeBaseQuality extends BaseModel
{
    use SoftDeletes;

    protected $fillable = [
        'name',
    ];

    public $orderable = [
        'id',
        'name',
    ];

    public $filterable = [
        'id',
        'name',
    ];

    public function knowledgeBaseItems()
    {
        return $this->hasMany(KnowledgeBaseItem::class, 'quality_id');
    }

    protected function serializeDate(DateTimeInterface $date): string
    {
        return $date->format(config('project.datetime_format') ?? 'Y-m-d H:i:s');
    }
}
