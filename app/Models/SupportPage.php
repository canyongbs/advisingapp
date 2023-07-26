<?php

namespace App\Models;

use Eloquent;
use Carbon\Carbon;
use DateTimeInterface;
use App\Support\HasAdvancedFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\SupportPage
 *
 * @property int $id
 * @property string $title
 * @property string $body
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 *
 * @method static Builder|SupportPage advancedFilter($data)
 * @method static Builder|SupportPage newModelQuery()
 * @method static Builder|SupportPage newQuery()
 * @method static Builder|SupportPage onlyTrashed()
 * @method static Builder|SupportPage query()
 * @method static Builder|SupportPage whereBody($value)
 * @method static Builder|SupportPage whereCreatedAt($value)
 * @method static Builder|SupportPage whereDeletedAt($value)
 * @method static Builder|SupportPage whereId($value)
 * @method static Builder|SupportPage whereTitle($value)
 * @method static Builder|SupportPage whereUpdatedAt($value)
 * @method static Builder|SupportPage withTrashed()
 * @method static Builder|SupportPage withoutTrashed()
 *
 * @mixin Eloquent
 */
class SupportPage extends BaseModel
{
    use HasAdvancedFilter;
    use SoftDeletes;

    public $orderable = [
        'id',
        'title',
    ];

    public $filterable = [
        'id',
        'title',
    ];

    protected $fillable = [
        'title',
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

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }
}
