<?php

namespace Assist\Case\Models;

use Carbon\Carbon;
use DateTimeInterface;
use App\Models\BaseModel;
use App\Support\HasAdvancedFilter;
use App\Models\IdeHelperCaseItemType;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @mixin IdeHelperCaseItemType
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
