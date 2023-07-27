<?php

namespace App\Models;

use Eloquent;
use Carbon\Carbon;
use DateTimeInterface;
use App\Support\HasAdvancedFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\EngagementInteractionDriver
 *
 * @property int $id
 * @property string $driver
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 *
 * @method static Builder|EngagementInteractionDriver advancedFilter($data)
 * @method static Builder|EngagementInteractionDriver newModelQuery()
 * @method static Builder|EngagementInteractionDriver newQuery()
 * @method static Builder|EngagementInteractionDriver onlyTrashed()
 * @method static Builder|EngagementInteractionDriver query()
 * @method static Builder|EngagementInteractionDriver whereCreatedAt($value)
 * @method static Builder|EngagementInteractionDriver whereDeletedAt($value)
 * @method static Builder|EngagementInteractionDriver whereDriver($value)
 * @method static Builder|EngagementInteractionDriver whereId($value)
 * @method static Builder|EngagementInteractionDriver whereUpdatedAt($value)
 * @method static Builder|EngagementInteractionDriver withTrashed()
 * @method static Builder|EngagementInteractionDriver withoutTrashed()
 *
 * @mixin Eloquent
 */
class EngagementInteractionDriver extends BaseModel
{
    use HasAdvancedFilter;
    use SoftDeletes;

    protected $fillable = [
        'driver',
    ];

    public $orderable = [
        'id',
        'driver',
    ];

    public $filterable = [
        'id',
        'driver',
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
