<?php

namespace App\Models;

use Eloquent;
use Carbon\Carbon;
use DateTimeInterface;
use App\Support\HasAdvancedFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\JourneyItem
 *
 * @property int $id
 * @property string|null $name
 * @property string $body
 * @property string $start
 * @property string $end
 * @property string $frequency
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read mixed $frequency_label
 *
 * @method static Builder|JourneyItem advancedFilter($data)
 * @method static Builder|JourneyItem newModelQuery()
 * @method static Builder|JourneyItem newQuery()
 * @method static Builder|JourneyItem onlyTrashed()
 * @method static Builder|JourneyItem query()
 * @method static Builder|JourneyItem whereBody($value)
 * @method static Builder|JourneyItem whereCreatedAt($value)
 * @method static Builder|JourneyItem whereDeletedAt($value)
 * @method static Builder|JourneyItem whereEnd($value)
 * @method static Builder|JourneyItem whereFrequency($value)
 * @method static Builder|JourneyItem whereId($value)
 * @method static Builder|JourneyItem whereName($value)
 * @method static Builder|JourneyItem whereStart($value)
 * @method static Builder|JourneyItem whereUpdatedAt($value)
 * @method static Builder|JourneyItem withTrashed()
 * @method static Builder|JourneyItem withoutTrashed()
 *
 * @mixin Eloquent
 * @mixin IdeHelperJourneyItem
 */
class JourneyItem extends BaseModel
{
    use HasAdvancedFilter;
    use SoftDeletes;

    public const FREQUENCY_RADIO = [
        '1' => 'Once',
        '2' => 'Each Day',
        '3' => 'Each Week',
        '4' => 'Each Month',
    ];

    public $orderable = [
        'id',
        'name',
        'start',
        'end',
    ];

    public $filterable = [
        'id',
        'name',
        'start',
        'end',
    ];

    protected $fillable = [
        'name',
        'body',
        'start',
        'end',
        'frequency',
    ];

    protected $dates = [
        'start',
        'end',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public function getStartAttribute($value)
    {
        return $value ? Carbon::createFromFormat('Y-m-d H:i:s', $value)->format(config('project.datetime_format')) : null;
    }

    public function setStartAttribute($value)
    {
        $this->attributes['start'] = $value ? Carbon::createFromFormat(config('project.datetime_format'), $value)->format('Y-m-d H:i:s') : null;
    }

    public function getEndAttribute($value)
    {
        return $value ? Carbon::createFromFormat('Y-m-d H:i:s', $value)->format(config('project.datetime_format')) : null;
    }

    public function setEndAttribute($value)
    {
        $this->attributes['end'] = $value ? Carbon::createFromFormat(config('project.datetime_format'), $value)->format('Y-m-d H:i:s') : null;
    }

    public function getFrequencyLabelAttribute($value)
    {
        return static::FREQUENCY_RADIO[$this->frequency] ?? null;
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

    protected function serializeDate(DateTimeInterface $date): string
    {
        return $date->format('Y-m-d H:i:s');
    }
}
