<?php

namespace Assist\Interaction\Models;

use App\Models\BaseModel;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Assist\Interaction\Models\Concerns\HasManyInteractions;
use Assist\Audit\Models\Concerns\Auditable as AuditableTrait;

/**
 * Assist\Interaction\Models\InteractionCampaign
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
 * @method static \Assist\Interaction\Database\Factories\InteractionCampaignFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|InteractionCampaign newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|InteractionCampaign newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|InteractionCampaign onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|InteractionCampaign query()
 * @method static \Illuminate\Database\Eloquent\Builder|InteractionCampaign whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InteractionCampaign whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InteractionCampaign whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InteractionCampaign whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InteractionCampaign whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InteractionCampaign withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|InteractionCampaign withoutTrashed()
 *
 * @mixin \Eloquent
 */
class InteractionCampaign extends BaseModel implements Auditable
{
    use AuditableTrait;
    use HasManyInteractions;
    use SoftDeletes;

    protected $fillable = [
        'name',
    ];
}
