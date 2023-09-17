<?php

namespace Assist\Prospect\Models;

use Eloquent;
use DateTimeInterface;
use App\Models\BaseModel;
use Assist\Audit\Models\Audit;
use Illuminate\Support\Carbon;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Assist\Audit\Models\Concerns\Auditable as AuditableTrait;

/**
 * Assist\Prospect\Models\ProspectSource
 *
 * @property string $id
 * @property string $name
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property-read Collection<int, Audit> $audits
 * @property-read int|null $audits_count
 * @property-read Collection<int, \Assist\Prospect\Models\Prospect> $prospects
 * @property-read int|null $prospects_count
 *
 * @method static \Assist\Prospect\Database\Factories\ProspectSourceFactory factory($count = null, $state = [])
 * @method static Builder|ProspectSource newModelQuery()
 * @method static Builder|ProspectSource newQuery()
 * @method static Builder|ProspectSource onlyTrashed()
 * @method static Builder|ProspectSource query()
 * @method static Builder|ProspectSource whereCreatedAt($value)
 * @method static Builder|ProspectSource whereDeletedAt($value)
 * @method static Builder|ProspectSource whereId($value)
 * @method static Builder|ProspectSource whereName($value)
 * @method static Builder|ProspectSource whereUpdatedAt($value)
 * @method static Builder|ProspectSource withTrashed()
 * @method static Builder|ProspectSource withoutTrashed()
 *
 * @mixin Eloquent
 * @mixin IdeHelperProspectSource
 */
class ProspectSource extends BaseModel implements Auditable
{
    use HasUuids;
    use SoftDeletes;
    use AuditableTrait;

    protected $fillable = [
        'name',
    ];

    public function prospects(): HasMany
    {
        return $this->hasMany(Prospect::class, 'source_id');
    }

    protected function serializeDate(DateTimeInterface $date): string
    {
        return $date->format(config('project.datetime_format') ?? 'Y-m-d H:i:s');
    }
}
