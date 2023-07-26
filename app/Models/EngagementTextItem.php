<?php

namespace App\Models;

use Eloquent;
use Carbon\Carbon;
use DateTimeInterface;
use App\Support\HasAdvancedFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\EngagementTextItem
 *
 * @property int $id
 * @property string|null $direction
 * @property int $mobile
 * @property string|null $message
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read mixed $direction_label
 *
 * @method static Builder|EngagementTextItem advancedFilter($data)
 * @method static Builder|EngagementTextItem newModelQuery()
 * @method static Builder|EngagementTextItem newQuery()
 * @method static Builder|EngagementTextItem onlyTrashed()
 * @method static Builder|EngagementTextItem query()
 * @method static Builder|EngagementTextItem whereCreatedAt($value)
 * @method static Builder|EngagementTextItem whereDeletedAt($value)
 * @method static Builder|EngagementTextItem whereDirection($value)
 * @method static Builder|EngagementTextItem whereId($value)
 * @method static Builder|EngagementTextItem whereMessage($value)
 * @method static Builder|EngagementTextItem whereMobile($value)
 * @method static Builder|EngagementTextItem whereUpdatedAt($value)
 * @method static Builder|EngagementTextItem withTrashed()
 * @method static Builder|EngagementTextItem withoutTrashed()
 *
 * @mixin Eloquent
 */
class EngagementTextItem extends BaseModel
{
    use HasAdvancedFilter;
    use SoftDeletes;

    public const DIRECTION_RADIO = [
        '1' => 'Inbound',
        '2' => 'Outbound',
    ];

    protected $fillable = [
        'mobile',
        'message',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public $orderable = [
        'id',
        'direction',
        'mobile',
        'message',
    ];

    public $filterable = [
        'id',
        'direction',
        'mobile',
        'message',
    ];

    public function getDirectionLabelAttribute($value)
    {
        return static::DIRECTION_RADIO[$this->direction] ?? null;
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
