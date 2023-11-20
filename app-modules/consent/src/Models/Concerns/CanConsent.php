<?php

/*
<COPYRIGHT>

Copyright © 2022-2023, Canyon GBS LLC

All rights reserved.

This file is part of a project developed using Laravel, which is an open-source framework for PHP.
Canyon GBS LLC acknowledges and respects the copyright of Laravel and other open-source
projects used in the development of this solution.

This project is licensed under the Affero General Public License (AGPL) 3.0.
For more details, see https://github.com/canyongbs/assistbycanyongbs/blob/main/LICENSE.

Notice:
- The copyright notice in this file and across all files and applications in this
 repository cannot be removed or altered without violating the terms of the AGPL 3.0 License.
- The software solution, including services, infrastructure, and code, is offered as a
 Software as a Service (SaaS) by Canyon GBS LLC.
- Use of this software implies agreement to the license terms and conditions as stated
 in the AGPL 3.0 License.

For more information or inquiries please visit our website at
https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

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
