<?php

namespace Assist\Case\Models;

use Eloquent;
use Carbon\Carbon;
use DateTimeInterface;
use App\Models\BaseModel;
use App\Models\RecordStudentItem;
use App\Support\HasAdvancedFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Assist\Case\Models\CaseUpdateItem
 *
 * @property int $id
 * @property string $update
 * @property string $internal
 * @property string $direction
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property int|null $student_id
 * @property int|null $case_id
 * @property-read \Assist\Case\Models\CaseItem|null $case
 * @property-read mixed $direction_label
 * @property-read mixed $internal_label
 * @property-read RecordStudentItem|null $student
 *
 * @method static Builder|CaseUpdateItem advancedFilter($data)
 * @method static Builder|CaseUpdateItem newModelQuery()
 * @method static Builder|CaseUpdateItem newQuery()
 * @method static Builder|CaseUpdateItem onlyTrashed()
 * @method static Builder|CaseUpdateItem query()
 * @method static Builder|CaseUpdateItem whereCaseId($value)
 * @method static Builder|CaseUpdateItem whereCreatedAt($value)
 * @method static Builder|CaseUpdateItem whereDeletedAt($value)
 * @method static Builder|CaseUpdateItem whereDirection($value)
 * @method static Builder|CaseUpdateItem whereId($value)
 * @method static Builder|CaseUpdateItem whereInternal($value)
 * @method static Builder|CaseUpdateItem whereStudentId($value)
 * @method static Builder|CaseUpdateItem whereUpdate($value)
 * @method static Builder|CaseUpdateItem whereUpdatedAt($value)
 * @method static Builder|CaseUpdateItem withTrashed()
 * @method static Builder|CaseUpdateItem withoutTrashed()
 *
 * @mixin Eloquent
 */
class CaseUpdateItem extends BaseModel
{
    use HasAdvancedFilter;
    use SoftDeletes;

    public const INTERNAL_RADIO = [
        'N' => 'No',
        'Y' => 'Yes',
    ];

    public const DIRECTION_RADIO = [
        '1' => 'Outbound',
        '2' => 'Inbound',
    ];

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
