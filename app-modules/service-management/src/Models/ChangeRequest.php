<?php

namespace AdvisingApp\ServiceManagement\Models;

use App\Models\BaseModel;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use AdvisingApp\Audit\Models\Concerns\Auditable as AuditableTrait;

class ChangeRequest extends BaseModel implements Auditable
{
    use AuditableTrait;

    public function type(): BelongsTo
    {
        return $this->belongsTo(ChangeRequestType::class, 'change_request_type_id');
    }

    public function status(): BelongsTo
    {
        return $this->belongsTo(ChangeRequestStatus::class, 'change_request_status_id');
    }
}
