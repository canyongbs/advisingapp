<?php

namespace Assist\Case\Models;

use Eloquent;
use DateTimeInterface;
use App\Models\BaseModel;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Assist\Case\Models\CaseItemStatus
 *
 * @property int $id
 * @property string $name
 * @property string $color
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property-read Collection<int, \Assist\Case\Models\CaseItem> $caseItems
 * @property-read int|null $case_items_count
 *
 * @method static \Assist\Case\Database\Factories\CaseItemStatusFactory factory($count = null, $state = [])
 * @method static Builder|CaseItemStatus newModelQuery()
 * @method static Builder|CaseItemStatus newQuery()
 * @method static Builder|CaseItemStatus onlyTrashed()
 * @method static Builder|CaseItemStatus query()
 * @method static Builder|CaseItemStatus whereColor($value)
 * @method static Builder|CaseItemStatus whereCreatedAt($value)
 * @method static Builder|CaseItemStatus whereDeletedAt($value)
 * @method static Builder|CaseItemStatus whereId($value)
 * @method static Builder|CaseItemStatus whereName($value)
 * @method static Builder|CaseItemStatus whereUpdatedAt($value)
 * @method static Builder|CaseItemStatus withTrashed()
 * @method static Builder|CaseItemStatus withoutTrashed()
 *
 * @mixin Eloquent
 */
class CaseItemStatus extends BaseModel
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'color',
    ];

    public function caseItems(): HasMany
    {
        return $this->hasMany(CaseItem::class, 'state_id');
    }

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format(config('project.datetime_format') ?? 'Y-m-d H:i:s');
    }
}
