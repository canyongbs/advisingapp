<?php

namespace App\Models;

use App\Support\HasAdvancedFilter;
use App\Traits\Auditable;
use Carbon\Carbon;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CaseItem extends Model
{
    use HasFactory, HasAdvancedFilter, SoftDeletes, Auditable;

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

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function student()
    {
        return $this->belongsTo(RecordStudentItem::class);
    }

    public function institution()
    {
        return $this->belongsTo(Institution::class);
    }

    public function state()
    {
        return $this->belongsTo(CaseItemStatus::class);
    }

    public function type()
    {
        return $this->belongsTo(CaseItemType::class);
    }

    public function priority()
    {
        return $this->belongsTo(CaseItemPriority::class);
    }

    public function assignedTo()
    {
        return $this->belongsTo(User::class);
    }

    public function createdBy()
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
}
