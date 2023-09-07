<?php

namespace Assist\Interaction\Models;

use App\Models\BaseModel;
use OwenIt\Auditing\Contracts\Auditable;
use Assist\Interaction\Models\Concerns\HasManyInteractions;
use Assist\Audit\Models\Concerns\Auditable as AuditableTrait;

class InteractionStatus extends BaseModel implements Auditable
{
    use AuditableTrait;
    use HasManyInteractions;

    protected $fillable = [
        'name',
        'color',
    ];
}
