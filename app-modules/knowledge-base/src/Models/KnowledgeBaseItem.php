<?php

namespace Assist\KnowledgeBase\Models;

use DateTimeInterface;
use App\Models\BaseModel;
use App\Models\Institution;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * Assist\KnowledgeBase\Models\KnowledgeBaseItem
 *
 * @property int $id
 * @property string $question
 * @property bool $public
 * @property string|null $solution
 * @property string|null $notes
 * @property int|null $quality_id
 * @property int|null $status_id
 * @property int|null $category_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Assist\KnowledgeBase\Models\KnowledgeBaseCategory|null $category
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Institution> $institution
 * @property-read int|null $institution_count
 * @property-read \Assist\KnowledgeBase\Models\KnowledgeBaseQuality|null $quality
 * @property-read \Assist\KnowledgeBase\Models\KnowledgeBaseStatus|null $status
 *
 * @method static \Assist\KnowledgeBase\Database\Factories\KnowledgeBaseItemFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|KnowledgeBaseItem newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|KnowledgeBaseItem newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|KnowledgeBaseItem onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|KnowledgeBaseItem query()
 * @method static \Illuminate\Database\Eloquent\Builder|KnowledgeBaseItem whereCategoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|KnowledgeBaseItem whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|KnowledgeBaseItem whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|KnowledgeBaseItem whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|KnowledgeBaseItem whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|KnowledgeBaseItem wherePublic($value)
 * @method static \Illuminate\Database\Eloquent\Builder|KnowledgeBaseItem whereQualityId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|KnowledgeBaseItem whereQuestion($value)
 * @method static \Illuminate\Database\Eloquent\Builder|KnowledgeBaseItem whereSolution($value)
 * @method static \Illuminate\Database\Eloquent\Builder|KnowledgeBaseItem whereStatusId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|KnowledgeBaseItem whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|KnowledgeBaseItem withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|KnowledgeBaseItem withoutTrashed()
 *
 * @mixin \Eloquent
 */
class KnowledgeBaseItem extends BaseModel
{
    use SoftDeletes;

    protected $casts = [
        'public' => 'boolean',
    ];

    public static $search = [
        'question',
    ];

    public $orderable = [
        'id',
        'question',
        'quality.rating',
        'status.status',
        'public',
        'category.category',
    ];

    public $filterable = [
        'id',
        'question',
        'quality.rating',
        'status.status',
        'public',
        'category.category',
    ];

    protected $fillable = [
        'question',
        'quality_id',
        'status_id',
        'public',
        'category_id',
        'solution',
        'notes',
    ];

    public function quality(): BelongsTo
    {
        return $this->belongsTo(KnowledgeBaseQuality::class);
    }

    public function status(): BelongsTo
    {
        return $this->belongsTo(KnowledgeBaseStatus::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(KnowledgeBaseCategory::class);
    }

    public function institution(): BelongsToMany
    {
        return $this->belongsToMany(Institution::class);
    }

    protected function serializeDate(DateTimeInterface $date): string
    {
        return $date->format(config('project.datetime_format') ?? 'Y-m-d H:i:s');
    }
}
