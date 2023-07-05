<?php

namespace App\Models;

use App\Support\HasAdvancedFilter;
use Carbon\Carbon;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    use HasFactory, HasAdvancedFilter;

    public $table = 'audit_logs';

    protected $dates = [
        'created_at',
        'updated_at',
    ];

    protected $fillable = [
        'description',
        'subject_id',
        'subject_type',
        'user_id',
        'properties',
        'host',
    ];

    public $orderable = [
        'id',
        'description',
        'subject_id',
        'subject_type',
        'user_id',
        'properties',
        'host',
        'created_at',
    ];

    public $filterable = [
        'id',
        'description',
        'subject_id',
        'subject_type',
        'user_id',
        'properties',
        'host',
        'created_at',
    ];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function getCreatedAtAttribute($value)
    {
        return $value ? Carbon::createFromFormat('Y-m-d H:i:s', $value)->format(config('project.datetime_format')) : null;
    }

    public function setCreatedAtAttribute($value)
    {
        $this->attributes['created_at'] = $value ? Carbon::createFromFormat(config('project.datetime_format'), $value)->format('Y-m-d H:i:s') : null;
    }

    public function getUpdatedAtAttribute($value)
    {
        return $value ? Carbon::createFromFormat('Y-m-d H:i:s', $value)->format(config('project.datetime_format')) : null;
    }

    public function setUpdatedAtAttribute($value)
    {
        $this->attributes['updated_at'] = $value ? Carbon::createFromFormat(config('project.datetime_format'), $value)->format('Y-m-d H:i:s') : null;
    }
}
