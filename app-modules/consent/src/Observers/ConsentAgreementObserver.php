<?php

namespace Assist\Consent\Observers;

use Assist\Consent\Models\ConsentAgreement;
use Assist\Consent\Models\UserConsentAgreement;

class ConsentAgreementObserver
{
    public function updated(ConsentAgreement $consentAgreement): void
    {
        if ($consentAgreement->users->count() > 0) {
            UserConsentAgreement::where('consent_agreement_id', $consentAgreement->id)
                ->delete();
        }
    }
}
