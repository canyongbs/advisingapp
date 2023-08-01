<?php

namespace App\Models;

use Eloquent;
use Carbon\Carbon;
use DateTimeInterface;
use App\Support\HasAdvancedFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\EngagementInteractionItem
 *
 * @property int $id
 * @property string $direction
 * @property string $start
 * @property string $duration
 * @property string $subject
 * @property string|null $description
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read mixed $direction_label
 * @property-read mixed $duration_label
 *
 * @method static Builder|EngagementInteractionItem advancedFilter($data)
 * @method static Builder|EngagementInteractionItem newModelQuery()
 * @method static Builder|EngagementInteractionItem newQuery()
 * @method static Builder|EngagementInteractionItem onlyTrashed()
 * @method static Builder|EngagementInteractionItem query()
 * @method static Builder|EngagementInteractionItem whereCreatedAt($value)
 * @method static Builder|EngagementInteractionItem whereDeletedAt($value)
 * @method static Builder|EngagementInteractionItem whereDescription($value)
 * @method static Builder|EngagementInteractionItem whereDirection($value)
 * @method static Builder|EngagementInteractionItem whereDuration($value)
 * @method static Builder|EngagementInteractionItem whereId($value)
 * @method static Builder|EngagementInteractionItem whereStart($value)
 * @method static Builder|EngagementInteractionItem whereSubject($value)
 * @method static Builder|EngagementInteractionItem whereUpdatedAt($value)
 * @method static Builder|EngagementInteractionItem withTrashed()
 * @method static Builder|EngagementInteractionItem withoutTrashed()
 *
 * @mixin Eloquent
 */
class EngagementInteractionItem extends BaseModel
{
    use HasAdvancedFilter;
    use SoftDeletes;

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
