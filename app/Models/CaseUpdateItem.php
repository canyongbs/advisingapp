<?php

namespace App\Models;

use App\Support\HasAdvancedFilter;
use App\Traits\Auditable;
use Carbon\Carbon;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CaseUpdateItem extends Model
{
    use HasFactory, HasAdvancedFilter, SoftDeletes, Auditable;

    public $table = 'case_update_items';

    public const INTERNAL_RADIO = [
        'N' => 'No',
        'Y' => 'Yes',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public const DIRECTION_RADIO = [
        '1' => 'Outbound',
        '2' => 'Inbound',
    ];

    protected $fillable = [
        'student_id',
        'case_id',
        'update',
        'internal',
        'direction',
    ];

    public $orderable = [
        'id',
        'student.full',
        'student.sisid',
        'student.otherid',
        'case.casenumber',
        'internal',
        'direction',
    ];

    public $filterable = [
        'id',
        'student.full',
        'student.sisid',
        'student.otherid',
        'case.casenumber',
        'internal',
        'direction',
    ];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function student()
    {
        return $this->belongsTo(RecordStudentItem::class);
    }

    public function case()
    {
        return $this->belongsTo(CaseItem::class);
    }

    public function getInternalLabelAttribute($value)
    {
        return static::INTERNAL_RADIO[$this->internal] ?? null;
    }

    public function getDirectionLabelAttribute($value)
    {
        return static::DIRECTION_RADIO[$this->direction] ?? null;
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
