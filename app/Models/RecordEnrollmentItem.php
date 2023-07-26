<?php

namespace App\Models;

use Eloquent;
use Carbon\Carbon;
use DateTimeInterface;
use App\Support\HasAdvancedFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\RecordEnrollmentItem
 *
 * @property int $id
 * @property string $sisid
 * @property string|null $name
 * @property string|null $start
 * @property string|null $end
 * @property string|null $course
 * @property float|null $grade
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 *
 * @method static Builder|RecordEnrollmentItem advancedFilter($data)
 * @method static Builder|RecordEnrollmentItem newModelQuery()
 * @method static Builder|RecordEnrollmentItem newQuery()
 * @method static Builder|RecordEnrollmentItem onlyTrashed()
 * @method static Builder|RecordEnrollmentItem query()
 * @method static Builder|RecordEnrollmentItem whereCourse($value)
 * @method static Builder|RecordEnrollmentItem whereCreatedAt($value)
 * @method static Builder|RecordEnrollmentItem whereDeletedAt($value)
 * @method static Builder|RecordEnrollmentItem whereEnd($value)
 * @method static Builder|RecordEnrollmentItem whereGrade($value)
 * @method static Builder|RecordEnrollmentItem whereId($value)
 * @method static Builder|RecordEnrollmentItem whereName($value)
 * @method static Builder|RecordEnrollmentItem whereSisid($value)
 * @method static Builder|RecordEnrollmentItem whereStart($value)
 * @method static Builder|RecordEnrollmentItem whereUpdatedAt($value)
 * @method static Builder|RecordEnrollmentItem withTrashed()
 * @method static Builder|RecordEnrollmentItem withoutTrashed()
 *
 * @mixin Eloquent
 */
class RecordEnrollmentItem extends BaseModel
{
    use HasAdvancedFilter;
    use SoftDeletes;

    protected $dates = [
        'start',
        'end',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $fillable = [
        'sisid',
        'name',
        'start',
        'end',
        'course',
        'grade',
    ];

    public $orderable = [
        'id',
        'sisid',
        'name',
        'start',
        'end',
        'course',
        'grade',
    ];

    public $filterable = [
        'id',
        'sisid',
        'name',
        'start',
        'end',
        'course',
        'grade',
    ];

    public function getStartAttribute($value)
    {
        return $value ? Carbon::createFromFormat('Y-m-d H:i:s', $value)->format(config('project.datetime_format')) : null;
    }

    public function setStartAttribute($value)
    {
        $this->attributes['start'] = $value ? Carbon::createFromFormat(config('project.datetime_format'), $value)->format('Y-m-d H:i:s') : null;
    }

    public function getEndAttribute($value)
    {
        return $value ? Carbon::createFromFormat('Y-m-d H:i:s', $value)->format(config('project.datetime_format')) : null;
    }

    public function setEndAttribute($value)
    {
        $this->attributes['end'] = $value ? Carbon::createFromFormat(config('project.datetime_format'), $value)->format('Y-m-d H:i:s') : null;
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
