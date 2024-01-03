<?php

namespace AdvisingApp\Authorization\Models;

use App\Models\User;
use App\Models\BaseModel;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Database\Eloquent\SoftDeletes;
use AdvisingApp\Authorization\Enums\LicenseType;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use AdvisingApp\Audit\Models\Concerns\Auditable as AuditableConcern;

/**
 * @mixin IdeHelperLicense
 */
class License extends BaseModel implements Auditable
{
    use AuditableConcern;
    use SoftDeletes;

    protected $fillable = [
        'type',
    ];

    protected $casts = [
        'type' => LicenseType::class,
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
