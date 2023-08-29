<?php

namespace App\Models;

use Eloquent;
use Carbon\Carbon;
use DateTimeInterface;
use App\Support\HasAdvancedFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\EngagementInteractionType
 *
 * @property int $id
 * @property string|null $type
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 *
 * @method static Builder|EngagementInteractionType advancedFilter($data)
 * @method static Builder|EngagementInteractionType newModelQuery()
 * @method static Builder|EngagementInteractionType newQuery()
 * @method static Builder|EngagementInteractionType onlyTrashed()
 * @method static Builder|EngagementInteractionType query()
 * @method static Builder|EngagementInteractionType whereCreatedAt($value)
 * @method static Builder|EngagementInteractionType whereDeletedAt($value)
 * @method static Builder|EngagementInteractionType whereId($value)
 * @method static Builder|EngagementInteractionType whereType($value)
 * @method static Builder|EngagementInteractionType whereUpdatedAt($value)
 * @method static Builder|EngagementInteractionType withTrashed()
 * @method static Builder|EngagementInteractionType withoutTrashed()
 *
 * @mixin Eloquent
 */
class EngagementInteractionType extends BaseModel
{
    use HasAdvancedFilter;
    use SoftDeletes;

    protected $fillable = [
        'type',
    ];

    public $orderable = [
        'id',
        'type',
    ];

    public $filterable = [
        'id',
        'type',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

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

    protected function serializeDate(DateTimeInterface $date): string
    {
        return $date->format('Y-m-d H:i:s');
    }
}
