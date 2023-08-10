<?php

namespace Assist\KnowledgeBase\Models;

use Eloquent;
use DateTimeInterface;
use App\Models\BaseModel;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;
use Assist\KnowledgeBase\Database\Factories\KnowledgeBaseCategoryFactory;

/**
 * Assist\KnowledgeBase\Models\KnowledgeBaseCategory
 *
 * @property int $id
 * @property string $name
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
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
class KnowledgeBaseCategory extends BaseModel
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
        return $this->hasMany(KnowledgeBaseItem::class, 'category_id');
    }

    protected function serializeDate(DateTimeInterface $date): string
    {
        return $date->format(config('project.datetime_format') ?? 'Y-m-d H:i:s');
    }
}
