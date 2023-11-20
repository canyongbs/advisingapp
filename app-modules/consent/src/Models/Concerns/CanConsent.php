<?php

namespace Assist\Consent\Models\Concerns;

use Assist\Audit\Overrides\BelongsToMany;
use Assist\Consent\Models\ConsentAgreement;
use Assist\Consent\Models\UserConsentAgreement;

trait CanConsent
{
    public function consentAgreements(): BelongsToMany
    {
        return $this->belongsToMany(ConsentAgreement::class, 'user_consent_agreements')
            ->using(UserConsentAgreement::class)
            ->withPivot('ip_address', 'deleted_at')
            ->withTimestamps();
    }

    public function hasConsentedTo(ConsentAgreement $agreement): bool
    {
        return $this->consentAgreements()
            ->where('consent_agreements.id', $agreement->id)
            ->whereNull('user_consent_agreements.deleted_at')
            ->exists();
    }

    public function hasNotConsentedTo(ConsentAgreement $agreement): bool
    {
        return ! $this->hasConsentedTo($agreement);
    }

    public function consentTo(ConsentAgreement $agreement): void
    {
        if ($this->hasConsentedTo($agreement)) {
            return;
        }

        $this->consentAgreements()->attach($agreement->id, [
            'ip_address' => request()->ip(),
        ]);
    }
}
