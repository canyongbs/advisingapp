<?php

namespace Assist\Division\Models;

use App\Models\User;
use App\Models\BaseModel;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Assist\Audit\Models\Concerns\Auditable as AuditableTrait;

class Division extends BaseModel implements Auditable
{
    use SoftDeletes;
    use AuditableTrait;

    protected $fillable = [
        'name',
        'code',
        'header',
        'footer',
    ];

    public function createdBy(): BelongsTo
    {
        return $this
            ->belongsTo(User::class);
    }

    public function lastUpdatedBy(): BelongsTo
    {
        return $this
            ->belongsTo(User::class);
    }
}
