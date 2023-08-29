<?php

namespace App\Models;

use Eloquent;
use Carbon\Carbon;
use DateTimeInterface;
use App\Support\HasAdvancedFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\JourneyEmailItem
 *
 * @property int $id
 * @property string $name
 * @property string $body
 * @property string $start
 * @property string|null $end
 * @property string|null $active
 * @property string $frequency
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read mixed $active_label
 * @property-read mixed $frequency_label
 *
 * @method static Builder|JourneyEmailItem advancedFilter($data)
 * @method static Builder|JourneyEmailItem newModelQuery()
 * @method static Builder|JourneyEmailItem newQuery()
 * @method static Builder|JourneyEmailItem onlyTrashed()
 * @method static Builder|JourneyEmailItem query()
 * @method static Builder|JourneyEmailItem whereActive($value)
 * @method static Builder|JourneyEmailItem whereBody($value)
 * @method static Builder|JourneyEmailItem whereCreatedAt($value)
 * @method static Builder|JourneyEmailItem whereDeletedAt($value)
 * @method static Builder|JourneyEmailItem whereEnd($value)
 * @method static Builder|JourneyEmailItem whereFrequency($value)
 * @method static Builder|JourneyEmailItem whereId($value)
 * @method static Builder|JourneyEmailItem whereName($value)
 * @method static Builder|JourneyEmailItem whereStart($value)
 * @method static Builder|JourneyEmailItem whereUpdatedAt($value)
 * @method static Builder|JourneyEmailItem withTrashed()
 * @method static Builder|JourneyEmailItem withoutTrashed()
 *
 * @mixin Eloquent
 */
class JourneyEmailItem extends BaseModel
{
    use HasAdvancedFilter;
    use SoftDeletes;

    public const ACTIVE_RADIO = [
        'N' => 'No',
        'Y' => 'Yes',
    ];

    public const FREQUENCY_RADIO = [
        '1' => 'One Time',
        '2' => 'Daily',
        '3' => 'Weekly',
        '4' => 'Monthly',
        '5' => 'Annually',
    ];

    public $orderable = [
        'id',
        'name',
        'start',
        'end',
        'active',
    ];

    public $filterable = [
        'id',
        'name',
        'start',
        'end',
        'active',
    ];

    protected $dates = [
        'start',
        'end',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $fillable = [
        'name',
        'body',
        'start',
        'end',
        'active',
        'frequency',
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

    public function getActiveLabelAttribute($value)
    {
        return static::ACTIVE_RADIO[$this->active] ?? null;
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
