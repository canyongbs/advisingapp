<?php

namespace App\Models;

use Eloquent;
use Carbon\Carbon;
use DateTimeInterface;
use App\Support\HasAdvancedFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\EngagementInteractionOutcome
 *
 * @property int $id
 * @property string $outcome
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 *
 * @method static Builder|EngagementInteractionOutcome advancedFilter($data)
 * @method static Builder|EngagementInteractionOutcome newModelQuery()
 * @method static Builder|EngagementInteractionOutcome newQuery()
 * @method static Builder|EngagementInteractionOutcome onlyTrashed()
 * @method static Builder|EngagementInteractionOutcome query()
 * @method static Builder|EngagementInteractionOutcome whereCreatedAt($value)
 * @method static Builder|EngagementInteractionOutcome whereDeletedAt($value)
 * @method static Builder|EngagementInteractionOutcome whereId($value)
 * @method static Builder|EngagementInteractionOutcome whereOutcome($value)
 * @method static Builder|EngagementInteractionOutcome whereUpdatedAt($value)
 * @method static Builder|EngagementInteractionOutcome withTrashed()
 * @method static Builder|EngagementInteractionOutcome withoutTrashed()
 *
 * @mixin Eloquent
 */
class EngagementInteractionOutcome extends BaseModel
{
    use HasAdvancedFilter;
    use SoftDeletes;

    protected $fillable = [
        'outcome',
    ];

    public $orderable = [
        'id',
        'outcome',
    ];

    public $filterable = [
        'id',
        'outcome',
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
