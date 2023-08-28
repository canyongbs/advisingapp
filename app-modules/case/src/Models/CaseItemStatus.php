<?php

namespace Assist\Case\Models;

use Eloquent;
use DateTimeInterface;
use App\Models\BaseModel;
use Assist\Audit\Models\Audit;
use Illuminate\Support\Carbon;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Assist\Case\Database\Factories\CaseItemStatusFactory;
use Assist\Audit\Models\Concerns\Auditable as AuditableTrait;

/**
 * Assist\Case\Models\CaseItemStatus
 *
 * @property string $id
 * @property string $name
 * @property string $color
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property-read Collection<int, Audit> $audits
 * @property-read int|null $audits_count
 * @property-read Collection<int, CaseItem> $caseItems
 * @property-read int|null $case_items_count
 *
 * @method static CaseItemStatusFactory factory($count = null, $state = [])
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
class CaseItemStatus extends BaseModel implements Auditable
{
    use SoftDeletes;
    use HasUuids;
    use AuditableTrait;

    protected $fillable = [
        'name',
        'color',
    ];

    public function caseItems(): HasMany
    {
        return $this->hasMany(CaseItem::class, 'status_id');
    }

    protected function serializeDate(DateTimeInterface $date): string
    {
        return $date->format(config('project.datetime_format') ?? 'Y-m-d H:i:s');
    }
}
