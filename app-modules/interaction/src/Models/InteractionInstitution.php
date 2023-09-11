<?php

namespace Assist\Interaction\Models;

use App\Models\BaseModel;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Assist\Interaction\Models\Concerns\HasManyInteractions;
use Assist\Audit\Models\Concerns\Auditable as AuditableTrait;

/**
 * Assist\Interaction\Models\InteractionInstitution
 *
 * @property string $id
 * @property string $name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Assist\Audit\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Assist\Interaction\Models\Interaction> $interactions
 * @property-read int|null $interactions_count
 * @method static \Assist\Interaction\Database\Factories\InteractionInstitutionFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|InteractionInstitution newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|InteractionInstitution newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|InteractionInstitution onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|InteractionInstitution query()
 * @method static \Illuminate\Database\Eloquent\Builder|InteractionInstitution whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InteractionInstitution whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InteractionInstitution whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InteractionInstitution whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InteractionInstitution whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InteractionInstitution withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|InteractionInstitution withoutTrashed()
 * @mixin \Eloquent
 */
class InteractionInstitution extends BaseModel implements Auditable
{
    use AuditableTrait;
    use HasManyInteractions;
    use SoftDeletes;

    protected $fillable = [
        'name',
    ];
}
