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
 * @mixin IdeHelperCaseUpdateItem
 */
class CaseUpdateItem extends Model
{
    use HasFactory;
    use HasAdvancedFilter;
    use SoftDeletes;
    use Auditable;

    public const INTERNAL_RADIO = [
        'N' => 'No',
        'Y' => 'Yes',
    ];

    public const DIRECTION_RADIO = [
        '1' => 'Outbound',
        '2' => 'Inbound',
    ];

    public $table = 'case_update_items';

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
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

    public function student(): BelongsTo
    {
        return $this->belongsTo(RecordStudentItem::class);
    }

    public function case(): BelongsTo
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

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }
}
