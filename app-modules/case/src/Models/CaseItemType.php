<?php

namespace Assist\Case\Models;

use Eloquent;
use DateTimeInterface;
use App\Models\BaseModel;
use Illuminate\Support\Carbon;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Auditable as AuditableTrait;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Assist\Case\Models\CaseItemType
 *
 * @property string $id
 * @property string $name
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property-read Collection<int, \OwenIt\Auditing\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read Collection<int, \Assist\Case\Models\CaseItem> $caseItems
 * @property-read int|null $case_items_count
 *
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
class CaseItemType extends BaseModel implements Auditable
{
    use SoftDeletes;
    use HasUuids;
    use AuditableTrait;

    protected $fillable = [
        'name',
    ];

    public function caseItems(): HasMany
    {
        return $this->hasMany(CaseItem::class, 'type_id');
    }

    protected function serializeDate(DateTimeInterface $date): string
    {
        return $date->format(config('project.datetime_format') ?? 'Y-m-d H:i:s');
    }
}
