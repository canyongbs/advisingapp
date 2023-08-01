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
 * App\Models\ProspectSource
 *
 * @property int $id
 * @property string $source
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 *
 * @method static Builder|ProspectSource advancedFilter($data)
 * @method static Builder|ProspectSource newModelQuery()
 * @method static Builder|ProspectSource newQuery()
 * @method static Builder|ProspectSource onlyTrashed()
 * @method static Builder|ProspectSource query()
 * @method static Builder|ProspectSource whereCreatedAt($value)
 * @method static Builder|ProspectSource whereDeletedAt($value)
 * @method static Builder|ProspectSource whereId($value)
 * @method static Builder|ProspectSource whereSource($value)
 * @method static Builder|ProspectSource whereUpdatedAt($value)
 * @method static Builder|ProspectSource withTrashed()
 * @method static Builder|ProspectSource withoutTrashed()
 *
 * @mixin Eloquent
 */
class ProspectSource extends BaseModel
{
    use HasAdvancedFilter;
    use SoftDeletes;
    use HasProspects;

    // TODO Enum to represent this value?
    protected $fillable = [
        'source',
    ];

    public $orderable = [
        'id',
        'source',
    ];

    public $filterable = [
        'id',
        'source',
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
