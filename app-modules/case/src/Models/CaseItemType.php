<?php

namespace Assist\Case\Models;

use Eloquent;
use Carbon\Carbon;
use DateTimeInterface;
use App\Models\BaseModel;
use App\Support\HasAdvancedFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Assist\Case\Models\CaseItemType
 *
 * @property int $id
 * @property string $type
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 *
 * @method static Builder|CaseItemType advancedFilter($data)
 * @method static Builder|CaseItemType newModelQuery()
 * @method static Builder|CaseItemType newQuery()
 * @method static Builder|CaseItemType onlyTrashed()
 * @method static Builder|CaseItemType query()
 * @method static Builder|CaseItemType whereCreatedAt($value)
 * @method static Builder|CaseItemType whereDeletedAt($value)
 * @method static Builder|CaseItemType whereId($value)
 * @method static Builder|CaseItemType whereType($value)
 * @method static Builder|CaseItemType whereUpdatedAt($value)
 * @method static Builder|CaseItemType withTrashed()
 * @method static Builder|CaseItemType withoutTrashed()
 *
 * @mixin Eloquent
 */
class CaseItemType extends BaseModel
{
    use HasAdvancedFilter;
    use SoftDeletes;

    protected $fillable = [
        'type',
    ];

    public $orderable = [
        'id',
        'type',
    ];

    public $filterable = [
        'id',
        'type',
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
