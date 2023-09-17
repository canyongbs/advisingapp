<?php

namespace Assist\KnowledgeBase\Models;

use Eloquent;
use DateTimeInterface;
use App\Models\BaseModel;
use App\Models\Institution;
use Assist\Audit\Models\Audit;
use Illuminate\Support\Carbon;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Assist\Audit\Models\Concerns\Auditable as AuditableTrait;

/**
 * Assist\KnowledgeBase\Models\KnowledgeBaseItem
 *
 * @property string $id
 * @property string $question
 * @property bool $public
 * @property string|null $solution
 * @property string|null $notes
 * @property string|null $quality_id
 * @property string|null $status_id
 * @property string|null $category_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property-read Collection<int, Audit> $audits
 * @property-read int|null $audits_count
 * @property-read \Assist\KnowledgeBase\Models\KnowledgeBaseCategory|null $category
 * @property-read Collection<int, Institution> $institution
 * @property-read int|null $institution_count
 * @property-read \Assist\KnowledgeBase\Models\KnowledgeBaseQuality|null $quality
 * @property-read \Assist\KnowledgeBase\Models\KnowledgeBaseStatus|null $status
 *
 * @method static \Assist\KnowledgeBase\Database\Factories\KnowledgeBaseItemFactory factory($count = null, $state = [])
 * @method static Builder|KnowledgeBaseItem newModelQuery()
 * @method static Builder|KnowledgeBaseItem newQuery()
 * @method static Builder|KnowledgeBaseItem onlyTrashed()
 * @method static Builder|KnowledgeBaseItem query()
 * @method static Builder|KnowledgeBaseItem whereCategoryId($value)
 * @method static Builder|KnowledgeBaseItem whereCreatedAt($value)
 * @method static Builder|KnowledgeBaseItem whereDeletedAt($value)
 * @method static Builder|KnowledgeBaseItem whereId($value)
 * @method static Builder|KnowledgeBaseItem whereNotes($value)
 * @method static Builder|KnowledgeBaseItem wherePublic($value)
 * @method static Builder|KnowledgeBaseItem whereQualityId($value)
 * @method static Builder|KnowledgeBaseItem whereQuestion($value)
 * @method static Builder|KnowledgeBaseItem whereSolution($value)
 * @method static Builder|KnowledgeBaseItem whereStatusId($value)
 * @method static Builder|KnowledgeBaseItem whereUpdatedAt($value)
 * @method static Builder|KnowledgeBaseItem withTrashed()
 * @method static Builder|KnowledgeBaseItem withoutTrashed()
 *
 * @mixin Eloquent
 * @mixin IdeHelperKnowledgeBaseItem
 */
class KnowledgeBaseItem extends BaseModel implements Auditable
{
    use SoftDeletes;
    use AuditableTrait;
    use HasUuids;

    protected $casts = [
        'public' => 'boolean',
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
