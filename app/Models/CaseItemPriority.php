<?php

namespace App\Models;

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

    protected function serializeDate(DateTimeInterface $date): string
    {
        return $date->format(config('project.datetime_format') ?? 'Y-m-d H:i:s');
    }
}
