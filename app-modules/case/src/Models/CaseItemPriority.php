<?php

namespace Assist\Case\Models;

use Eloquent;
use DateTimeInterface;
use App\Models\BaseModel;
use Illuminate\Support\Carbon;
use OwenIt\Auditing\Models\Audit;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Assist\Case\Database\Factories\CaseItemPriorityFactory;
use Assist\Audit\Models\Concerns\Auditable as AuditableTrait;

/**
 * Assist\Case\Models\CaseItemPriority
 *
 * @property string $id
 * @property string $name
 * @property int $order
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property-read Collection<int, Audit> $audits
 * @property-read int|null $audits_count
 * @property-read Collection<int, CaseItem> $caseItems
 * @property-read int|null $case_items_count
 *
 * @method static CaseItemPriorityFactory factory($count = null, $state = [])
 * @method static Builder|CaseItemPriority newModelQuery()
 * @method static Builder|CaseItemPriority newQuery()
 * @method static Builder|CaseItemPriority onlyTrashed()
 * @method static Builder|CaseItemPriority query()
 * @method static Builder|CaseItemPriority whereCreatedAt($value)
 * @method static Builder|CaseItemPriority whereDeletedAt($value)
 * @method static Builder|CaseItemPriority whereId($value)
 * @method static Builder|CaseItemPriority whereName($value)
 * @method static Builder|CaseItemPriority whereOrder($value)
 * @method static Builder|CaseItemPriority whereUpdatedAt($value)
 * @method static Builder|CaseItemPriority withTrashed()
 * @method static Builder|CaseItemPriority withoutTrashed()
 *
 * @mixin Eloquent
 */
class CaseItemPriority extends BaseModel implements Auditable
{
    use SoftDeletes;
    use HasUuids;
    use AuditableTrait;

    protected $fillable = [
        'name',
        'order',
    ];

    public function caseItems(): HasMany
    {
        return $this->hasMany(CaseItem::class, 'priority_id');
    }

    protected function serializeDate(DateTimeInterface $date): string
    {
        return $date->format(config('project.datetime_format') ?? 'Y-m-d H:i:s');
    }
}
