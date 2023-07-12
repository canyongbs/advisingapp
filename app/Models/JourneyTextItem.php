<?php

namespace App\Models;

use Carbon\Carbon;
use DateTimeInterface;
use App\Traits\Auditable;
use App\Support\HasAdvancedFilter;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @mixin IdeHelperJourneyTextItem
 */
class JourneyTextItem extends Model
{
    use HasFactory;
    use HasAdvancedFilter;
    use SoftDeletes;
    use Auditable;

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

    public $table = 'journey_text_items';

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

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }
}
