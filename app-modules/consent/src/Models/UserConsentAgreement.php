<?php

namespace Assist\Consent\Models;

use App\Models\BaseModel;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\Concerns\AsPivot;
use Assist\Audit\Models\Concerns\Auditable as AuditableTrait;

/**
 * @mixin IdeHelperUserConsentAgreement
 */
class UserConsentAgreement extends BaseModel implements Auditable
{
    use AsPivot;
    use AuditableTrait;
    use SoftDeletes;

    protected $table = 'user_consent_agreements';

    protected $fillable = [
        'consent_agreement_id',
        'ip_address',
    ];
}
