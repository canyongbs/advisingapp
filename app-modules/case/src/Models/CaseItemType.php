<?php

namespace Assist\Case\Models;

use Eloquent;
use DateTimeInterface;
use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Assist\Case\Models\CaseItemType
 *
 * @property int $id
 * @property string $name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Assist\Case\Models\CaseItem> $caseItems
 * @property-read int|null $case_items_count
 *
 * @method static Builder|CaseItemType advancedFilter($data)
 * @method static \Assist\Case\Database\Factories\CaseItemTypeFactory factory($count = null, $state = [])
 * @method static Builder|CaseItemType newModelQuery()
 * @method static Builder|CaseItemType newQuery()
 * @method static Builder|CaseItemType onlyTrashed()
 * @method static Builder|CaseItemType query()
 * @method static Builder|CaseItemType whereCreatedAt($value)
 * @method static Builder|CaseItemType whereDeletedAt($value)
 * @method static Builder|CaseItemType whereId($value)
 * @method static Builder|CaseItemType whereName($value)
 * @method static Builder|CaseItemType whereUpdatedAt($value)
 * @method static Builder|CaseItemType withTrashed()
 * @method static Builder|CaseItemType withoutTrashed()
 *
 * @mixin Eloquent
 */
class CaseItemType extends BaseModel
{
    use SoftDeletes;

    protected $fillable = [
        'name',
    ];

    public function caseItems()
    {
        return $this->hasMany(CaseItem::class, 'type_id');
    }

    protected function serializeDate(DateTimeInterface $date): string
    {
        return $date->format(config('project.datetime_format') ?? 'Y-m-d H:i:s');
    }
}
