<?php

namespace AdvisingApp\ServiceManagement\Models;

use App\Models\BaseModel;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use AdvisingApp\Audit\Models\Concerns\Auditable as AuditableTrait;

class ChangeRequestType extends BaseModel implements Auditable
{
    use AuditableTrait;

    public function changeRequests(): HasMany
    {
        return $this->hasMany(ChangeRequest::class);
    }
}
