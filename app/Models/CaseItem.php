<?php

namespace App\Models;

use DateTimeInterface;
use App\Support\HasAdvancedFilter;
use Kirschbaum\PowerJoins\PowerJoins;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @mixin IdeHelperCaseItem
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
            ownerKey: 'student_id',
        );
    }

    // TODO
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

    // TODO
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    protected function serializeDate(DateTimeInterface $date): string
    {
        return $date->format(config('project.datetime_format') ?? 'Y-m-d H:i:s');
    }
}
