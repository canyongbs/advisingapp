<?php

namespace Assist\Consent\Models\Concerns;

use Assist\Audit\Overrides\BelongsToMany;
use Assist\Consent\Models\ConsentAgreement;

trait CanConsent
{
    public function consentAgreements(): BelongsToMany
    {
        return $this->belongsToMany(ConsentAgreement::class)
            ->withPivot('request')
            ->withTimestamps();
    }

    public function hasNotConsentedTo(ConsentAgreement $agreement): bool
    {
        return ! $this->hasConsentedTo($agreement);
    }

    public function hasConsentedTo(ConsentAgreement $agreement): bool
    {
        return $this->belongsToMany(ConsentAgreement::class)
            ->where('id', $agreement->id)
            ->exists();
    }

    public function consentTo(ConsentAgreement $agreement): void
    {
        if ($this->hasConsentedTo($agreement)) {
            return;
        }

        $this->consentAgreements()->attach($agreement->id, [
            'request' => request()->ip(),
        ]);
    }
}
