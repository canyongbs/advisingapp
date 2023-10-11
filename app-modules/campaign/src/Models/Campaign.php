<?php

namespace Assist\Campaign\Models;

use App\Models\BaseModel;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Assist\Audit\Models\Concerns\Auditable as AuditableTrait;

class Campaign extends BaseModel implements Auditable
{
    use AuditableTrait;
    use SoftDeletes;

    protected $fillable = [
        'caseload_id',
        'name',
        'execution_time',
    ];

    public function actions(): HasMany
    {
        return $this->hasMany(CampaignAction::class);
    }
}
