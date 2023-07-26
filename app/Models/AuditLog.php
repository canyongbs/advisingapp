<?php

namespace App\Models;

use Eloquent;
use Carbon\Carbon;
use DateTimeInterface;
use App\Support\HasAdvancedFilter;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use App\Models\Concerns\DefinesPermissions;
use Illuminate\Database\Eloquent\Factories\HasFactory;

// TODO To delete when we install auditable library
/**
 * App\Models\AuditLog
 *
 * @property int $id
 * @property string $description
 * @property int|null $subject_id
 * @property string|null $subject_type
 * @property int|null $user_id
 * @property string|null $properties
 * @property string|null $host
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @method static Builder|AuditLog advancedFilter($data)
 * @method static Builder|AuditLog newModelQuery()
 * @method static Builder|AuditLog newQuery()
 * @method static Builder|AuditLog query()
 * @method static Builder|AuditLog whereCreatedAt($value)
 * @method static Builder|AuditLog whereDescription($value)
 * @method static Builder|AuditLog whereHost($value)
 * @method static Builder|AuditLog whereId($value)
 * @method static Builder|AuditLog whereProperties($value)
 * @method static Builder|AuditLog whereSubjectId($value)
 * @method static Builder|AuditLog whereSubjectType($value)
 * @method static Builder|AuditLog whereUpdatedAt($value)
 * @method static Builder|AuditLog whereUserId($value)
 *
 * @mixin Eloquent
 */
class AuditLog extends Model
{
    use HasFactory;
    use HasAdvancedFilter;
    use DefinesPermissions;

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

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }
}
