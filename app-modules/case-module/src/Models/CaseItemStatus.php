<?php

namespace Assist\CaseModule\Models;

use DateTimeInterface;
use App\Models\BaseModel;
use App\Support\HasAdvancedFilter;
use App\Models\IdeHelperCaseItemStatus;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @mixin IdeHelperCaseItemStatus
 */
class CaseItemStatus extends BaseModel
{
    use HasAdvancedFilter;
    use SoftDeletes;

    protected $fillable = [
        'name',
        'color',
    ];

    public $orderable = [
        'id',
        'name',
    ];

    public $filterable = [
        'id',
        'name',
    ];

    public function caseItems()
    {
        return $this->hasMany(CaseItem::class);
    }

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format(config('project.datetime_format') ?? 'Y-m-d H:i:s');
    }
}
