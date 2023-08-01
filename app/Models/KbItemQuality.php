<?php

namespace App\Models;

use Eloquent;
use Carbon\Carbon;
use DateTimeInterface;
use App\Support\HasAdvancedFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\KbItemQuality
 *
 * @property int $id
 * @property string $rating
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 *
 * @method static Builder|KbItemQuality advancedFilter($data)
 * @method static Builder|KbItemQuality newModelQuery()
 * @method static Builder|KbItemQuality newQuery()
 * @method static Builder|KbItemQuality onlyTrashed()
 * @method static Builder|KbItemQuality query()
 * @method static Builder|KbItemQuality whereCreatedAt($value)
 * @method static Builder|KbItemQuality whereDeletedAt($value)
 * @method static Builder|KbItemQuality whereId($value)
 * @method static Builder|KbItemQuality whereRating($value)
 * @method static Builder|KbItemQuality whereUpdatedAt($value)
 * @method static Builder|KbItemQuality withTrashed()
 * @method static Builder|KbItemQuality withoutTrashed()
 *
 * @mixin Eloquent
 */
class KbItemQuality extends BaseModel
{
    use HasAdvancedFilter;
    use SoftDeletes;

    protected $fillable = [
        'rating',
    ];

    public $orderable = [
        'id',
        'rating',
    ];

    public $filterable = [
        'id',
        'rating',
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
