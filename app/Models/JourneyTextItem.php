<?php

namespace App\Models;

use Eloquent;
use Carbon\Carbon;
use DateTimeInterface;
use App\Support\HasAdvancedFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\JourneyTextItem
 *
 * @property int $id
 * @property string|null $name
 * @property string|null $text
 * @property string|null $start
 * @property string|null $end
 * @property string $active
 * @property string|null $frequency
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read mixed $active_label
 * @property-read mixed $frequency_label
 *
 * @method static Builder|JourneyTextItem advancedFilter($data)
 * @method static Builder|JourneyTextItem newModelQuery()
 * @method static Builder|JourneyTextItem newQuery()
 * @method static Builder|JourneyTextItem onlyTrashed()
 * @method static Builder|JourneyTextItem query()
 * @method static Builder|JourneyTextItem whereActive($value)
 * @method static Builder|JourneyTextItem whereCreatedAt($value)
 * @method static Builder|JourneyTextItem whereDeletedAt($value)
 * @method static Builder|JourneyTextItem whereEnd($value)
 * @method static Builder|JourneyTextItem whereFrequency($value)
 * @method static Builder|JourneyTextItem whereId($value)
 * @method static Builder|JourneyTextItem whereName($value)
 * @method static Builder|JourneyTextItem whereStart($value)
 * @method static Builder|JourneyTextItem whereText($value)
 * @method static Builder|JourneyTextItem whereUpdatedAt($value)
 * @method static Builder|JourneyTextItem withTrashed()
 * @method static Builder|JourneyTextItem withoutTrashed()
 *
 * @mixin Eloquent
 * @mixin IdeHelperJourneyTextItem
 */
class JourneyTextItem extends BaseModel
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
        '5' => 'Yearly',
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
        'text',
        'start',
        'end',
        'active',
        'frequency',
    ];

    public $orderable = [
        'id',
        'name',
        'text',
        'start',
        'end',
        'active',
        'frequency',
    ];

    public $filterable = [
        'id',
        'name',
        'text',
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
