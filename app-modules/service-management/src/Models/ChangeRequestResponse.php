<?php

namespace AdvisingApp\ServiceManagement\Models;

use App\Models\User;
use App\Models\BaseModel;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use AdvisingApp\Audit\Models\Concerns\Auditable as AuditableTrait;

class ChangeRequestResponse extends BaseModel implements Auditable
{
    use AuditableTrait;

    protected $fillable = [
        'approved',
        'user_id',
    ];

    protected $casts = [
        'approved' => 'boolean',
    ];

    public function changeRequest(): BelongsTo
    {
        return $this->belongsTo(ChangeRequest::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
