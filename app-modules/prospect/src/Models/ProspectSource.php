<?php

namespace Assist\Prospect\Models;

use Eloquent;
use DateTimeInterface;
use App\Models\BaseModel;
use App\Support\HasAdvancedFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;
use Assist\Prospect\Models\Concerns\HasProspects;

/**
 * Assist\Prospect\Models\ProspectSource
 *
 * @property int $id
 * @property string $source
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Assist\Prospect\Models\Prospect> $prospects
 * @property-read int|null $prospects_count
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

    protected function serializeDate(DateTimeInterface $date): string
    {
        return $date->format(config('project.datetime_format') ?? 'Y-m-d H:i:s');
    }
}
