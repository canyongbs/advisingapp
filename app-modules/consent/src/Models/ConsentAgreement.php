<?php

namespace Assist\Consent\Models;

use App\Models\User;
use App\Models\BaseModel;
use OwenIt\Auditing\Contracts\Auditable;
use Assist\Audit\Overrides\BelongsToMany;
use Assist\Consent\Enums\ConsentAgreementType;
use Assist\Audit\Models\Concerns\Auditable as AuditableTrait;

/**
 * @mixin IdeHelperConsentAgreement
 */
class ConsentAgreement extends BaseModel implements Auditable
{
    use AuditableTrait;

    protected $casts = [
        'type' => ConsentAgreementType::class,
    ];

    protected $fillable = [
        'title',
        'description',
        'body',
    ];

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_consent_agreements')
            ->using(UserConsentAgreement::class)
            ->withPivot('ip_address', 'deleted_at')
            ->withTimestamps();
    }
}
