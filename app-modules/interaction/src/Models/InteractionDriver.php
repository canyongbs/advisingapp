<?php

namespace Assist\Interaction\Models;

use App\Models\BaseModel;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Assist\Interaction\Models\Concerns\HasManyInteractions;
use Assist\Audit\Models\Concerns\Auditable as AuditableTrait;

/**
 * Assist\Interaction\Models\InteractionDriver
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
 * @method static \Assist\Interaction\Database\Factories\InteractionDriverFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|InteractionDriver newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|InteractionDriver newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|InteractionDriver onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|InteractionDriver query()
 * @method static \Illuminate\Database\Eloquent\Builder|InteractionDriver whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InteractionDriver whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InteractionDriver whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InteractionDriver whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InteractionDriver whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InteractionDriver withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|InteractionDriver withoutTrashed()
 *
 * @mixin \Eloquent
 */
class InteractionDriver extends BaseModel implements Auditable
{
    use AuditableTrait;
    use HasManyInteractions;
    use SoftDeletes;

    protected $fillable = [
        'name',
    ];
}
