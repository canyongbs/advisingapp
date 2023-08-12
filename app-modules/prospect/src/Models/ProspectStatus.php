<?php

namespace Assist\Prospect\Models;

use Eloquent;
use DateTimeInterface;
use App\Models\BaseModel;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Assist\Prospect\Database\Factories\ProspectStatusFactory;

/**
 * Assist\Prospect\Models\ProspectStatus
 *
 * @property int $id
 * @property string $name
 * @property string $color
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property-read Collection<int, Prospect> $prospects
 * @property-read int|null $prospects_count
 *
 * @method static ProspectStatusFactory factory($count = null, $state = [])
 * @method static Builder|ProspectStatus newModelQuery()
 * @method static Builder|ProspectStatus newQuery()
 * @method static Builder|ProspectStatus onlyTrashed()
 * @method static Builder|ProspectStatus query()
 * @method static Builder|ProspectStatus whereColor($value)
 * @method static Builder|ProspectStatus whereCreatedAt($value)
 * @method static Builder|ProspectStatus whereDeletedAt($value)
 * @method static Builder|ProspectStatus whereId($value)
 * @method static Builder|ProspectStatus whereName($value)
 * @method static Builder|ProspectStatus whereUpdatedAt($value)
 * @method static Builder|ProspectStatus withTrashed()
 * @method static Builder|ProspectStatus withoutTrashed()
 *
 * @mixin Eloquent
 */
class ProspectStatus extends BaseModel
{
    use HasUuids;
    use SoftDeletes;

    protected $fillable = [
        'name',
        'color',
    ];

    public function prospects(): HasMany
    {
        return $this->hasMany(Prospect::class, 'status_id');
    }

    protected function serializeDate(DateTimeInterface $date): string
    {
        return $date->format(config('project.datetime_format') ?? 'Y-m-d H:i:s');
    }
}
