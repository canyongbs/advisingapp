<?php

namespace App\Models;

use Eloquent;
use Carbon\Carbon;
use DateTimeInterface;
use App\Support\HasAdvancedFilter;
use Illuminate\Database\Eloquent\Builder;

/**
 * App\Models\RecordProgramItem
 *
 * @property int $id
 * @property string $name
 * @property string|null $institution
 * @property string|null $plan
 * @property string|null $career
 * @property string|null $term
 * @property string|null $status
 * @property string|null $foi
 * @property float|null $gpa
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read mixed $deleted_at
 *
 * @method static Builder|RecordProgramItem advancedFilter($data)
 * @method static Builder|RecordProgramItem newModelQuery()
 * @method static Builder|RecordProgramItem newQuery()
 * @method static Builder|RecordProgramItem query()
 * @method static Builder|RecordProgramItem whereCareer($value)
 * @method static Builder|RecordProgramItem whereCreatedAt($value)
 * @method static Builder|RecordProgramItem whereFoi($value)
 * @method static Builder|RecordProgramItem whereGpa($value)
 * @method static Builder|RecordProgramItem whereId($value)
 * @method static Builder|RecordProgramItem whereInstitution($value)
 * @method static Builder|RecordProgramItem whereName($value)
 * @method static Builder|RecordProgramItem wherePlan($value)
 * @method static Builder|RecordProgramItem whereStatus($value)
 * @method static Builder|RecordProgramItem whereTerm($value)
 * @method static Builder|RecordProgramItem whereUpdatedAt($value)
 *
 * @mixin Eloquent
 */
class RecordProgramItem extends BaseModel
{
    use HasAdvancedFilter;

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $fillable = [
        'name',
        'institution',
        'plan',
        'career',
        'term',
        'status',
        'foi',
        'gpa',
    ];

    public $orderable = [
        'id',
        'name',
        'institution',
        'plan',
        'career',
        'term',
        'status',
        'foi',
        'gpa',
    ];

    public $filterable = [
        'id',
        'name',
        'institution',
        'plan',
        'career',
        'term',
        'status',
        'foi',
        'gpa',
    ];

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

    protected function serializeDate(DateTimeInterface $date): string
    {
        return $date->format('Y-m-d H:i:s');
    }
}
