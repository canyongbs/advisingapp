<?php

namespace Assist\KnowledgeBase\Models;

use Eloquent;
use DateTimeInterface;
use App\Models\BaseModel;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;
use Assist\KnowledgeBase\Database\Factories\KnowledgeBaseStatusFactory;

/**
 * Assist\KnowledgeBase\Models\KnowledgeBaseStatus
 *
 * @property int $id
 * @property string $name
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
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
class KnowledgeBaseStatus extends BaseModel
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
        return $this->hasMany(KnowledgeBaseItem::class, 'status_id');
    }

    protected function serializeDate(DateTimeInterface $date): string
    {
        return $date->format(config('project.datetime_format') ?? 'Y-m-d H:i:s');
    }
}
