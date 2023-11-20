<?php

namespace Assist\Interaction\Models;

use App\Models\BaseModel;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Assist\Interaction\Enums\InteractionStatusColorOptions;
use Assist\Interaction\Models\Concerns\HasManyInteractions;
use Assist\Audit\Models\Concerns\Auditable as AuditableTrait;

/**
 * @mixin IdeHelperInteractionStatus
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
