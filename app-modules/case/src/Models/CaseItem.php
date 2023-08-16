<?php

namespace Assist\Case\Models;

use Eloquent;
use App\Models\User;
use DateTimeInterface;
use App\Models\BaseModel;
use App\Models\Institution;
use Illuminate\Support\Carbon;
use Kirschbaum\PowerJoins\PowerJoins;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Assist\Case\Database\Factories\CaseItemFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Assist\Audit\Models\Concerns\Auditable as AuditableTrait;

/**
 * Assist\Case\Models\CaseItem
 *
 * @property string $id
 * @property int $casenumber
 * @property string|null $respondent_type
 * @property string|null $respondent_id
 * @property string|null $close_details
 * @property string|null $res_details
 * @property string|null $institution_id
 * @property string|null $status_id
 * @property string|null $type_id
 * @property string|null $priority_id
 * @property string|null $assigned_to_id
 * @property string|null $created_by_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property-read User|null $assignedTo
 * @property-read Collection<int, \OwenIt\Auditing\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read Collection<int, \Assist\Case\Models\CaseUpdate> $caseUpdates
 * @property-read int|null $case_updates_count
 * @property-read User|null $createdBy
 * @property-read Institution|null $institution
 * @property-read CaseItemPriority|null $priority
 * @property-read Model|Eloquent $respondent
 * @property-read CaseItemStatus|null $status
 * @property-read CaseItemType|null $type
 *
 * @method static CaseItemFactory factory($count = null, $state = [])
 * @method static Builder|CaseItem newModelQuery()
 * @method static Builder|CaseItem newQuery()
 * @method static Builder|CaseItem onlyTrashed()
 * @method static Builder|CaseItem query()
 * @method static Builder|CaseItem whereAssignedToId($value)
 * @method static Builder|CaseItem whereCasenumber($value)
 * @method static Builder|CaseItem whereCloseDetails($value)
 * @method static Builder|CaseItem whereCreatedAt($value)
 * @method static Builder|CaseItem whereCreatedById($value)
 * @method static Builder|CaseItem whereDeletedAt($value)
 * @method static Builder|CaseItem whereId($value)
 * @method static Builder|CaseItem whereInstitutionId($value)
 * @method static Builder|CaseItem wherePriorityId($value)
 * @method static Builder|CaseItem whereResDetails($value)
 * @method static Builder|CaseItem whereRespondentId($value)
 * @method static Builder|CaseItem whereRespondentType($value)
 * @method static Builder|CaseItem whereStatusId($value)
 * @method static Builder|CaseItem whereTypeId($value)
 * @method static Builder|CaseItem whereUpdatedAt($value)
 * @method static Builder|CaseItem withTrashed()
 * @method static Builder|CaseItem withoutTrashed()
 *
 * @mixin Eloquent
 */
class CaseItem extends BaseModel implements Auditable
{
    use SoftDeletes;
    use PowerJoins;
    use AuditableTrait;
    use HasUuids;

    protected $fillable = [
        'casenumber',
        'respondent_type',
        'respondent_id',
        'institution_id',
        'status_id',
        'type_id',
        'priority_id',
        'assigned_to_id',
        'close_details',
        'res_details',
        'created_by_id',
    ];

    public function respondent(): MorphTo
    {
        return $this->morphTo(
            name: 'respondent',
            type: 'respondent_type',
            id: 'respondent_id',
        );
    }

    public function institution(): BelongsTo
    {
        return $this->belongsTo(Institution::class, 'institution_id');
    }

    public function caseUpdates(): HasMany
    {
        return $this->hasMany(CaseUpdate::class, 'case_id');
    }

    public function status(): BelongsTo
    {
        return $this->belongsTo(CaseItemStatus::class);
    }

    public function type(): BelongsTo
    {
        return $this->belongsTo(CaseItemType::class);
    }

    public function priority(): BelongsTo
    {
        return $this->belongsTo(CaseItemPriority::class);
    }

    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    protected function serializeDate(DateTimeInterface $date): string
    {
        return $date->format(config('project.datetime_format') ?? 'Y-m-d H:i:s');
    }
}
