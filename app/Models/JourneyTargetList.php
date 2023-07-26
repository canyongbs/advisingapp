<?php

namespace App\Models;

use Eloquent;
use Carbon\Carbon;
use DateTimeInterface;
use App\Support\HasAdvancedFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\JourneyTargetList
 *
 * @property int $id
 * @property string|null $name
 * @property string|null $description
 * @property string|null $query
 * @property int|null $population
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 *
 * @method static Builder|JourneyTargetList advancedFilter($data)
 * @method static Builder|JourneyTargetList newModelQuery()
 * @method static Builder|JourneyTargetList newQuery()
 * @method static Builder|JourneyTargetList onlyTrashed()
 * @method static Builder|JourneyTargetList query()
 * @method static Builder|JourneyTargetList whereCreatedAt($value)
 * @method static Builder|JourneyTargetList whereDeletedAt($value)
 * @method static Builder|JourneyTargetList whereDescription($value)
 * @method static Builder|JourneyTargetList whereId($value)
 * @method static Builder|JourneyTargetList whereName($value)
 * @method static Builder|JourneyTargetList wherePopulation($value)
 * @method static Builder|JourneyTargetList whereQuery($value)
 * @method static Builder|JourneyTargetList whereUpdatedAt($value)
 * @method static Builder|JourneyTargetList withTrashed()
 * @method static Builder|JourneyTargetList withoutTrashed()
 *
 * @mixin Eloquent
 */
class JourneyTargetList extends BaseModel
{
    use HasAdvancedFilter;
    use SoftDeletes;

    public $orderable = [
        'id',
        'name',
        'population',
    ];

    public $filterable = [
        'id',
        'name',
        'population',
    ];

    protected $fillable = [
        'name',
        'description',
        'query',
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
