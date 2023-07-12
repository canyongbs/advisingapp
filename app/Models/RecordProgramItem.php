<?php

namespace App\Models;

use Carbon\Carbon;
use DateTimeInterface;
use App\Support\HasAdvancedFilter;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @mixin IdeHelperRecordProgramItem
 */
class RecordProgramItem extends Model
{
    use HasFactory;
    use HasAdvancedFilter;

    public $table = 'record_program_items';

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

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }
}
