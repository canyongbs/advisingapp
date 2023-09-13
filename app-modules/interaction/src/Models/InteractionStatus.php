<?php

namespace Assist\Interaction\Models;

use App\Models\BaseModel;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Assist\Interaction\Enums\InteractionStatusColorOptions;
use Assist\Interaction\Models\Concerns\HasManyInteractions;
use Assist\Audit\Models\Concerns\Auditable as AuditableTrait;

/**
 * Assist\Interaction\Models\InteractionStatus
 *
 * @property string $id
 * @property string $name
 * @property InteractionStatusColorOptions $color
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Assist\Audit\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Assist\Interaction\Models\Interaction> $interactions
 * @property-read int|null $interactions_count
 *
 * @method static \Assist\Interaction\Database\Factories\InteractionStatusFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|InteractionStatus newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|InteractionStatus newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|InteractionStatus onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|InteractionStatus query()
 * @method static \Illuminate\Database\Eloquent\Builder|InteractionStatus whereColor($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InteractionStatus whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InteractionStatus whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InteractionStatus whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InteractionStatus whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InteractionStatus whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InteractionStatus withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|InteractionStatus withoutTrashed()
 *
 * @mixin \Eloquent
 */
class InteractionStatus extends BaseModel implements Auditable
{
    use AuditableTrait;
    use HasManyInteractions;
    use SoftDeletes;

    protected $fillable = [
        'name',
        'color',
    ];

    protected $casts = [
        'color' => InteractionStatusColorOptions::class,
    ];
}
