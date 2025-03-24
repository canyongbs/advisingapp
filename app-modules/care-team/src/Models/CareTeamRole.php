<?php

namespace AdvisingApp\CareTeam\Models;

use App\Enums\CareTeamRoleType;
use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use AdvisingApp\Audit\Models\Concerns\Auditable as AuditableTrait;
use OwenIt\Auditing\Contracts\Auditable;

class CareTeamRole extends BaseModel implements Auditable
{
    use HasFactory;
    use SoftDeletes;
    use AuditableTrait;

    protected $fillable = [
        'name',
        'type',
        'is_default',
    ];

    protected $casts = [
        'type' => CareTeamRoleType::class,
    ];
}
