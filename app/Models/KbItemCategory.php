<?php

namespace App\Models;

use Eloquent;
use Carbon\Carbon;
use DateTimeInterface;
use App\Support\HasAdvancedFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\KbItemCategory
 *
 * @property int $id
 * @property string $category
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 *
 * @method static Builder|KbItemCategory advancedFilter($data)
 * @method static Builder|KbItemCategory newModelQuery()
 * @method static Builder|KbItemCategory newQuery()
 * @method static Builder|KbItemCategory onlyTrashed()
 * @method static Builder|KbItemCategory query()
 * @method static Builder|KbItemCategory whereCategory($value)
 * @method static Builder|KbItemCategory whereCreatedAt($value)
 * @method static Builder|KbItemCategory whereDeletedAt($value)
 * @method static Builder|KbItemCategory whereId($value)
 * @method static Builder|KbItemCategory whereUpdatedAt($value)
 * @method static Builder|KbItemCategory withTrashed()
 * @method static Builder|KbItemCategory withoutTrashed()
 *
 * @mixin Eloquent
 */
class KbItemCategory extends BaseModel
{
    use HasAdvancedFilter;
    use SoftDeletes;

    protected $fillable = [
        'category',
    ];

    public $orderable = [
        'id',
        'category',
    ];

    public $filterable = [
        'id',
        'category',
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
