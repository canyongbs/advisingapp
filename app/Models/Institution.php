<?php

namespace App\Models;

use Eloquent;
use DateTimeInterface;
use Illuminate\Support\Carbon;
use App\Support\HasAdvancedFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

/**
 * App\Models\Institution
 *
 * @property string $id
 * @property string|null $code
 * @property string $name
 * @property string|null $description
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 *
 * @method static Builder|Institution advancedFilter($data)
 * @method static \Database\Factories\InstitutionFactory factory($count = null, $state = [])
 * @method static Builder|Institution newModelQuery()
 * @method static Builder|Institution newQuery()
 * @method static Builder|Institution onlyTrashed()
 * @method static Builder|Institution query()
 * @method static Builder|Institution whereCode($value)
 * @method static Builder|Institution whereCreatedAt($value)
 * @method static Builder|Institution whereDeletedAt($value)
 * @method static Builder|Institution whereDescription($value)
 * @method static Builder|Institution whereId($value)
 * @method static Builder|Institution whereName($value)
 * @method static Builder|Institution whereUpdatedAt($value)
 * @method static Builder|Institution withTrashed()
 * @method static Builder|Institution withoutTrashed()
 *
 * @mixin Eloquent
 */
class Institution extends BaseModel
{
    use HasAdvancedFilter;
    use SoftDeletes;
    use HasUuids;

    protected $fillable = [
        'code',
        'name',
        'description',
    ];

    protected function serializeDate(DateTimeInterface $date): string
    {
        return $date->format(config('project.datetime_format') ?? 'Y-m-d H:i:s');
    }
}
