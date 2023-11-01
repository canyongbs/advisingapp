<?php

namespace Assist\Consent\Observers;

use Assist\Consent\Models\ConsentAgreement;
use Assist\Consent\Models\UserConsentAgreement;

class ConsentAgreementObserver
{
    public function updated(ConsentAgreement $consentAgreement): void
    {
        $consentAgreement->userConsentAgreements->each(fn (UserConsentAgreement $consentAgreement) => $consentAgreement->delete());
    }
}
