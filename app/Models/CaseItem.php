<?php

namespace App\Models;

use Carbon\Carbon;
use DateTimeInterface;
use App\Traits\Auditable;
use App\Support\HasAdvancedFilter;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @mixin IdeHelperCaseItem
 */
class CaseItem extends Model
{
    use HasFactory;
    use HasAdvancedFilter;
    use SoftDeletes;
    use Auditable;

    public $table = 'case_items';

    public static $search = [
        'casenumber',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
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
        'student_id',
        'institution_id',
        'state_id',
        'type_id',
        'priority_id',
        'assigned_to_id',
        'close_details',
        'res_details',
        'created_by_id',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(RecordStudentItem::class);
    }

    public function institution(): BelongsTo
    {
        return $this->belongsTo(Institution::class);
    }

    public function state(): BelongsTo
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

    public function getCreatedAtAttribute($value)
    {
        return $value ? Carbon::createFromFormat('Y-m-d H:i:s', $value)->format(config('project.datetime_format')) : null;
    }

    public function getUpdatedAtAttribute($value)
    {
        return $value ? Carbon::createFromFormat('Y-m-d H:i:s', $value)->format(config('project.datetime_format')) : null;
    }

    public function getDeletedAtAttribute($value)
    {
        return $value ? Carbon::createFromFormat('Y-m-d H:i:s', $value)->format(config('project.datetime_format')) : null;
    }

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }
}
