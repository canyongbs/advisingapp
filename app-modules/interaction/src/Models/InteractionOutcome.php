<?php

namespace Assist\Interaction\Models;

use App\Models\BaseModel;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Assist\Interaction\Models\Concerns\HasManyInteractions;
use Assist\Audit\Models\Concerns\Auditable as AuditableTrait;

/**
 * Assist\Interaction\Models\InteractionOutcome
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
 *
 * @method static \Assist\Interaction\Database\Factories\InteractionOutcomeFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|InteractionOutcome newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|InteractionOutcome newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|InteractionOutcome onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|InteractionOutcome query()
 * @method static \Illuminate\Database\Eloquent\Builder|InteractionOutcome whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InteractionOutcome whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InteractionOutcome whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InteractionOutcome whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InteractionOutcome whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InteractionOutcome withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|InteractionOutcome withoutTrashed()
 *
 * @mixin \Eloquent
 */
class InteractionOutcome extends BaseModel implements Auditable
{
    use AuditableTrait;
    use HasManyInteractions;
    use SoftDeletes;

    protected $fillable = [
        'name',
    ];
}
