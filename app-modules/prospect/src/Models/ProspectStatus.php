<?php

namespace Assist\Prospect\Models;

use Eloquent;
use Carbon\Carbon;
use DateTimeInterface;
use App\Models\BaseModel;
use App\Support\HasAdvancedFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;
use Assist\Prospect\Models\Concerns\HasProspects;

/**
 * Assist\Prospect\Models\ProspectStatus
 *
 * @property int $id
 * @property string $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Assist\Prospect\Models\Prospect> $prospects
 * @property-read int|null $prospects_count
 *
 * @method static Builder|ProspectStatus advancedFilter($data)
 * @method static Builder|ProspectStatus newModelQuery()
 * @method static Builder|ProspectStatus newQuery()
 * @method static Builder|ProspectStatus onlyTrashed()
 * @method static Builder|ProspectStatus query()
 * @method static Builder|ProspectStatus whereCreatedAt($value)
 * @method static Builder|ProspectStatus whereDeletedAt($value)
 * @method static Builder|ProspectStatus whereId($value)
 * @method static Builder|ProspectStatus whereStatus($value)
 * @method static Builder|ProspectStatus whereUpdatedAt($value)
 * @method static Builder|ProspectStatus withTrashed()
 * @method static Builder|ProspectStatus withoutTrashed()
 *
 * @mixin Eloquent
 */
class ProspectStatus extends BaseModel
{
    use HasAdvancedFilter;
    use SoftDeletes;
    use HasProspects;

    protected $fillable = [
        'status',
    ];

    public $orderable = [
        'id',
        'status',
    ];

    public $filterable = [
        'id',
        'status',
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
