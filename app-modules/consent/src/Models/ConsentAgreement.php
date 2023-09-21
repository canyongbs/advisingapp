<?php

namespace Assist\Consent\Models;

use App\Models\User;
use App\Models\BaseModel;
use Assist\Audit\Overrides\BelongsToMany;
use Assist\Consent\Enums\ConsentAgreementType;

/**
 * @mixin IdeHelperConsentAgreement
 */
class ConsentAgreement extends BaseModel
{
    protected $casts = [
        'type' => ConsentAgreementType::class,
    ];

    protected $fillable = [
        'title',
        'description',
        'body',
    ];

    public function user(): BelongsToMany
    {
        return $this->belongsToMany(User::class)
            ->withPivot('ip_address')
            ->withTimestamps();
    }
}
