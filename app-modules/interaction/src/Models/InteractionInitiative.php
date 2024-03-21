<?php

namespace AdvisingApp\Interaction\Models;

use App\Models\BaseModel;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use AdvisingApp\Interaction\Models\Concerns\HasManyInteractions;
use AdvisingApp\Audit\Models\Concerns\Auditable as AuditableTrait;

class InteractionInitiative extends BaseModel implements Auditable
{
    use AuditableTrait;
    use HasManyInteractions;
    use HasFactory;

    protected $fillable = [
        'name',
    ];
}
