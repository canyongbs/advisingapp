<?php

namespace App\Models;

use Eloquent;
use Carbon\Carbon;
use DateTimeInterface;
use App\Support\HasAdvancedFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\KbItemStatus
 *
 * @property int $id
 * @property string $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 *
 * @method static Builder|KbItemStatus advancedFilter($data)
 * @method static Builder|KbItemStatus newModelQuery()
 * @method static Builder|KbItemStatus newQuery()
 * @method static Builder|KbItemStatus onlyTrashed()
 * @method static Builder|KbItemStatus query()
 * @method static Builder|KbItemStatus whereCreatedAt($value)
 * @method static Builder|KbItemStatus whereDeletedAt($value)
 * @method static Builder|KbItemStatus whereId($value)
 * @method static Builder|KbItemStatus whereStatus($value)
 * @method static Builder|KbItemStatus whereUpdatedAt($value)
 * @method static Builder|KbItemStatus withTrashed()
 * @method static Builder|KbItemStatus withoutTrashed()
 *
 * @mixin Eloquent
 */
class KbItemStatus extends BaseModel
{
    use HasAdvancedFilter;
    use SoftDeletes;

    protected $fillable = [
        'status',
    ];

    public $orderable = [
        'id',
        'status',
    ];

    public $filterable = [
        'id',
        'status',
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
