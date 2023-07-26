<?php

namespace App\Models;

use Eloquent;
use Carbon\Carbon;
use DateTimeInterface;
use App\Support\HasAdvancedFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * App\Models\KbItem
 *
 * @property int $id
 * @property string $question
 * @property string $public
 * @property string|null $solution
 * @property string|null $notes
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property int|null $quality_id
 * @property int|null $status_id
 * @property int|null $category_id
 * @property-read KbItemCategory|null $category
 * @property-read mixed $public_label
 * @property-read Collection<int, Institution> $institution
 * @property-read int|null $institution_count
 * @property-read KbItemQuality|null $quality
 * @property-read KbItemStatus|null $status
 *
 * @method static Builder|KbItem advancedFilter($data)
 * @method static Builder|KbItem newModelQuery()
 * @method static Builder|KbItem newQuery()
 * @method static Builder|KbItem onlyTrashed()
 * @method static Builder|KbItem query()
 * @method static Builder|KbItem whereCategoryId($value)
 * @method static Builder|KbItem whereCreatedAt($value)
 * @method static Builder|KbItem whereDeletedAt($value)
 * @method static Builder|KbItem whereId($value)
 * @method static Builder|KbItem whereNotes($value)
 * @method static Builder|KbItem wherePublic($value)
 * @method static Builder|KbItem whereQualityId($value)
 * @method static Builder|KbItem whereQuestion($value)
 * @method static Builder|KbItem whereSolution($value)
 * @method static Builder|KbItem whereStatusId($value)
 * @method static Builder|KbItem whereUpdatedAt($value)
 * @method static Builder|KbItem withTrashed()
 * @method static Builder|KbItem withoutTrashed()
 *
 * @mixin Eloquent
 */
class KbItem extends BaseModel
{
    use HasAdvancedFilter;
    use SoftDeletes;

    public const PUBLIC_RADIO = [
        'N' => 'No',
        'Y' => 'Yes',
    ];

    public static $search = [
        'question',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
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
        return $this->belongsTo(KbItemQuality::class);
    }

    public function status(): BelongsTo
    {
        return $this->belongsTo(KbItemStatus::class);
    }

    public function getPublicLabelAttribute($value)
    {
        return static::PUBLIC_RADIO[$this->public] ?? null;
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(KbItemCategory::class);
    }

    public function institution(): BelongsToMany
    {
        return $this->belongsToMany(Institution::class);
    }

    public function getCreatedAtAttribute($value)
    {
        return $value ? Carbon::createFromFormat('Y-m-d H:i:s', $value)->format(config('project.datetime_format')) : null;
    }

    public function getUpdatedAtAttribute($value)
    {
        return $value ? Carbon::createFromFormat('Y-m-d H:i:s', $value)->format(config('project.datetime_format')) : null;
    }

    public function getDeletedAtAttribute($value)
    {
        return $value ? Carbon::createFromFormat('Y-m-d H:i:s', $value)->format(config('project.datetime_format')) : null;
    }

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }
}
