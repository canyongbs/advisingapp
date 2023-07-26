<?php

namespace App\Models;

use Eloquent;
use Carbon\Carbon;
use DateTimeInterface;
use App\Support\HasAdvancedFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * App\Models\UserAlert
 *
 * @property int $id
 * @property string|null $message
 * @property string|null $link
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read Collection<int, User> $users
 * @property-read int|null $users_count
 *
 * @method static Builder|UserAlert advancedFilter($data)
 * @method static Builder|UserAlert newModelQuery()
 * @method static Builder|UserAlert newQuery()
 * @method static Builder|UserAlert onlyTrashed()
 * @method static Builder|UserAlert query()
 * @method static Builder|UserAlert whereCreatedAt($value)
 * @method static Builder|UserAlert whereDeletedAt($value)
 * @method static Builder|UserAlert whereId($value)
 * @method static Builder|UserAlert whereLink($value)
 * @method static Builder|UserAlert whereMessage($value)
 * @method static Builder|UserAlert whereUpdatedAt($value)
 * @method static Builder|UserAlert withTrashed()
 * @method static Builder|UserAlert withoutTrashed()
 *
 * @mixin Eloquent
 */
class UserAlert extends BaseModel
{
    use HasAdvancedFilter;
    use SoftDeletes;

    protected $fillable = [
        'message',
        'link',
    ];

    public $orderable = [
        'id',
        'message',
        'link',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public $filterable = [
        'id',
        'message',
        'link',
        'users.name',
    ];

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class);
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
