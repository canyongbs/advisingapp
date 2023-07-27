<?php

namespace Assist\Case\Models;

use Eloquent;
use App\Models\User;
use DateTimeInterface;
use App\Models\BaseModel;
use App\Models\Institution;
use Illuminate\Support\Carbon;
use App\Support\HasAdvancedFilter;
use Kirschbaum\PowerJoins\PowerJoins;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Assist\Case\Models\CaseItem
 *
 * @property int $id
 * @property int $casenumber
 * @property string|null $respondent_type
 * @property int|null $respondent_id
 * @property string|null $close_details
 * @property string|null $res_details
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property int|null $institution_id
 * @property int|null $state_id
 * @property int|null $type_id
 * @property int|null $priority_id
 * @property int|null $assigned_to_id
 * @property int|null $created_by_id
 * @property-read User|null $assignedTo
 * @property-read User|null $createdBy
 * @property-read Institution|null $institution
 * @property-read \Assist\Case\Models\CaseItemPriority|null $priority
 * @property-read Model|\Eloquent $respondent
 * @property-read \Assist\Case\Models\CaseItemStatus|null $state
 * @property-read \Assist\Case\Models\CaseItemType|null $type
 *
 * @method static Builder|CaseItem advancedFilter($data)
 * @method static \Assist\Case\Database\Factories\CaseItemFactory factory($count = null, $state = [])
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
 * @method static Builder|CaseItem whereStateId($value)
 * @method static Builder|CaseItem whereTypeId($value)
 * @method static Builder|CaseItem whereUpdatedAt($value)
 * @method static Builder|CaseItem withTrashed()
 * @method static Builder|CaseItem withoutTrashed()
 *
 * @mixin Eloquent
 */
class CaseItem extends BaseModel
{
    use HasAdvancedFilter;
    use SoftDeletes;
    use PowerJoins;

    public static $search = [
        'casenumber',
    ];

    public $orderable = [
        'id',
        'casenumber',
        'student.full',
        'student.sisid',
        'student.otherid',
        'institution.name',
        'assigned_to.name',
    ];

    public $filterable = [
        'id',
        'casenumber',
        'student.full',
        'student.sisid',
        'student.otherid',
        'institution.name',
        'assigned_to.name',
    ];

    protected $fillable = [
        'casenumber',
        'respondent_type',
        'respondent_id',
        'institution_id',
        'state_id',
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
            ownerKey: 'sisid',
        );
    }

    public function institution(): BelongsTo
    {
        return $this->belongsTo(Institution::class);
    }

    public function state(): BelongsTo
    {
        return $this->belongsTo(CaseItemStatus::class);
    }

    // TODO
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

    // TODO: Figure this out and whether or not it can just be handled via auditing
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    protected function serializeDate(DateTimeInterface $date): string
    {
        return $date->format(config('project.datetime_format') ?? 'Y-m-d H:i:s');
    }
}
