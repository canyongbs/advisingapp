<?php

namespace App\Models;

use Carbon\Carbon;
use DateTimeInterface;
use App\Support\HasAdvancedFilter;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @mixin IdeHelperCaseItemPriority
 */
class CaseItemPriority extends BaseModel
{
    use HasAdvancedFilter;
    use SoftDeletes;

    protected $fillable = [
        'name',
        'order',
    ];

    public $orderable = [
        'id',
        'order',
    ];

    public $filterable = [
        'id',
        'order',
    ];

    public function caseItems(): HasMany
    {
        return $this->hasMany(CaseItem::class);
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
