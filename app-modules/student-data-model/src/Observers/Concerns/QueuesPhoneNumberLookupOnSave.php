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

namespace AdvisingApp\StudentDataModel\Observers\Concerns;

use AdvisingApp\StudentDataModel\Contracts\PhoneNumberLookupService;
use AdvisingApp\StudentDataModel\Jobs\LookupPhoneNumber;
use AdvisingApp\StudentDataModel\Models\PhoneNumberLookup;
use Illuminate\Database\Eloquent\Model;

/**
 * Observer concern that queues a Telnyx lookup whenever a phone-number record
 * is first introduced or its `number` changes — provided no lookup result
 * exists for that number yet and the lookup service is configured.
 *
 * Used by both Student and Prospect phone-number observers; the model is
 * expected to expose a `number` attribute.
 */
trait QueuesPhoneNumberLookupOnSave
{
    public function saved(Model $phoneNumber): void
    {
        if (! $phoneNumber->wasRecentlyCreated && ! $phoneNumber->wasChanged('number')) {
            return;
        }

        $number = $phoneNumber->getAttribute('number');

        if (! is_string($number) || blank($number)) {
            return;
        }

        if (! app(PhoneNumberLookupService::class)->isConfigured()) {
            return;
        }

        // Reuse an existing lookup result rather than paying for another.
        if (PhoneNumberLookup::query()->where('number', $number)->exists()) {
            return;
        }

        dispatch(new LookupPhoneNumber($number))->afterCommit();
    }
}
