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
 * @mixin IdeHelperEngagementInteractionItem
 */
class EngagementInteractionItem extends Model
{
    use HasFactory;
    use HasAdvancedFilter;
    use SoftDeletes;
    use Auditable;

    public const DIRECTION_RADIO = [
        'inbound' => 'Inbound',
        'outbound' => 'Outbound',
    ];

    public const DURATION_RADIO = [
        '5' => '5 Minutes',
        '15' => '15 Minutes',
        '30' => '30 Minutes',
        '45' => '45 Minutes',
        '60' => '1 Hour',
        '90' => '1.5 Hours',
        '120' => '2 Hours',
    ];

    public $table = 'engagement_interaction_items';

    public $orderable = [
        'id',
        'direction',
        'subject',
    ];

    public $filterable = [
        'id',
        'direction',
        'subject',
    ];

    protected $dates = [
        'start',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $fillable = [
        'direction',
        'start',
        'duration',
        'subject',
        'description',
    ];

    public function getDirectionLabelAttribute($value)
    {
        return static::DIRECTION_RADIO[$this->direction] ?? null;
    }

    public function getStartAttribute($value)
    {
        return $value ? Carbon::createFromFormat('Y-m-d H:i:s', $value)->format(config('project.datetime_format')) : null;
    }

    public function setStartAttribute($value)
    {
        $this->attributes['start'] = $value ? Carbon::createFromFormat(config('project.datetime_format'), $value)->format('Y-m-d H:i:s') : null;
    }

    public function getDurationLabelAttribute($value)
    {
        return static::DURATION_RADIO[$this->duration] ?? null;
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
