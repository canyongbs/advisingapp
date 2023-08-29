<?php

namespace App\Models;

use Eloquent;
use Carbon\Carbon;
use DateTimeInterface;
use App\Support\HasAdvancedFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\EngagementEmailItem
 *
 * @property int $id
 * @property string $email
 * @property string $subject
 * @property string $body
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 *
 * @method static Builder|EngagementEmailItem advancedFilter($data)
 * @method static Builder|EngagementEmailItem newModelQuery()
 * @method static Builder|EngagementEmailItem newQuery()
 * @method static Builder|EngagementEmailItem onlyTrashed()
 * @method static Builder|EngagementEmailItem query()
 * @method static Builder|EngagementEmailItem whereBody($value)
 * @method static Builder|EngagementEmailItem whereCreatedAt($value)
 * @method static Builder|EngagementEmailItem whereDeletedAt($value)
 * @method static Builder|EngagementEmailItem whereEmail($value)
 * @method static Builder|EngagementEmailItem whereId($value)
 * @method static Builder|EngagementEmailItem whereSubject($value)
 * @method static Builder|EngagementEmailItem whereUpdatedAt($value)
 * @method static Builder|EngagementEmailItem withTrashed()
 * @method static Builder|EngagementEmailItem withoutTrashed()
 *
 * @mixin Eloquent
 */
class EngagementEmailItem extends BaseModel
{
    use HasAdvancedFilter;
    use SoftDeletes;

    protected $fillable = [
        'email',
        'subject',
        'body',
    ];

    public $orderable = [
        'id',
        'email',
        'subject',
        'body',
    ];

    public $filterable = [
        'id',
        'email',
        'subject',
        'body',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
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
