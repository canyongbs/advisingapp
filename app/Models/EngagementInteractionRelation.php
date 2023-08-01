<?php

namespace App\Models;

use Eloquent;
use Carbon\Carbon;
use DateTimeInterface;
use App\Support\HasAdvancedFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\EngagementInteractionRelation
 *
 * @property int $id
 * @property string $relation
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 *
 * @method static Builder|EngagementInteractionRelation advancedFilter($data)
 * @method static Builder|EngagementInteractionRelation newModelQuery()
 * @method static Builder|EngagementInteractionRelation newQuery()
 * @method static Builder|EngagementInteractionRelation onlyTrashed()
 * @method static Builder|EngagementInteractionRelation query()
 * @method static Builder|EngagementInteractionRelation whereCreatedAt($value)
 * @method static Builder|EngagementInteractionRelation whereDeletedAt($value)
 * @method static Builder|EngagementInteractionRelation whereId($value)
 * @method static Builder|EngagementInteractionRelation whereRelation($value)
 * @method static Builder|EngagementInteractionRelation whereUpdatedAt($value)
 * @method static Builder|EngagementInteractionRelation withTrashed()
 * @method static Builder|EngagementInteractionRelation withoutTrashed()
 *
 * @mixin Eloquent
 */
class EngagementInteractionRelation extends BaseModel
{
    use HasAdvancedFilter;
    use SoftDeletes;

    protected $fillable = [
        'relation',
    ];

    public $orderable = [
        'id',
        'relation',
    ];

    public $filterable = [
        'id',
        'relation',
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

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }
}
