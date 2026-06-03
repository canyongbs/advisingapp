<?php

/*
<COPYRIGHT>

    Copyright © 2016-2026, Canyon GBS Inc. All rights reserved.

    Advising App® is licensed under the Elastic License 2.0. For more details,
    see https://github.com/canyongbs/advisingapp/blob/main/LICENSE.

    Notice:

    - You may not provide the software to third parties as a hosted or managed
      service, where the service provides users with access to any substantial set of
      the features or functionality of the software.
    - You may not move, change, disable, or circumvent the license key functionality
      in the software, and you may not remove or obscure any functionality in the
      software that is protected by the license key.
    - You may not alter, remove, or obscure any licensing, copyright, or other notices
      of the licensor in the software. Any use of the licensor’s trademarks is subject
      to applicable law.
    - Canyon GBS Inc. respects the intellectual property rights of others and expects the
      same in return. Canyon GBS® and Advising App® are registered trademarks of
      Canyon GBS Inc., and we are committed to enforcing and protecting our trademarks
      vigorously.
    - The software solution, including services, infrastructure, and code, is offered as a
      Software as a Service (SaaS) by Canyon GBS Inc.
    - Use of this software implies agreement to the license terms and conditions as stated
      in the Elastic License 2.0.

    For more information or inquiries please visit our website at
    https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

namespace AdvisingApp\StudentDataModel\Models\Concerns;

use AdvisingApp\StudentDataModel\Enums\PhoneNumberLookupStatus;
use App\Features\PhoneNumberLookupFeature;
use Illuminate\Database\Eloquent\Builder;

/**
 * Used by StudentPhoneNumber and ProspectPhoneNumber; both expose the
 * `smsOptOut`, `bounced`, and `phoneNumberLookup` relationships keyed by
 * `number`.
 *
 * Textability is gated per-tenant by the PhoneNumberLookupFeature Pennant
 * flag so the rollout can be staged:
 *
 *   - Feature ON  (the future-state path): a number is textable when it has
 *     no opt-out, no bounce, AND a Telnyx lookup row exists with a textable
 *     status. No row = not yet scanned = NOT textable.
 *   - Feature OFF (legacy path): the original three-way AND — `can_receive_sms`
 *     true AND no opt-out AND no bounce. The `can_receive_sms` column is
 *     still written via the existing manual entry / SIS sync / importer
 *     paths and continues to drive the gate until a tenant opts in.
 */
trait IsTextable
{
    public function isTextable(): bool
    {
        if ($this->smsOptOut()->exists()) {
            return false;
        }

        if ($this->bounced()->exists()) {
            return false;
        }

        if (PhoneNumberLookupFeature::active()) {
            return $this->phoneNumberLookup()
                ->whereIn('status', PhoneNumberLookupStatus::textableStatuses())
                ->exists();
        }

        return (bool) $this->getAttribute('can_receive_sms');
    }

    /**
     * @param Builder<static> $query
     */
    public function scopeTextable(Builder $query): void
    {
        $query->whereDoesntHave('smsOptOut')->whereDoesntHave('bounced');

        if (PhoneNumberLookupFeature::active()) {
            $query->whereHas(
                'phoneNumberLookup',
                fn (Builder $lookup) => $lookup->whereIn('status', PhoneNumberLookupStatus::textableStatuses()),
            );

            return;
        }

        $query->where('can_receive_sms', true);
    }
}
