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

namespace AdvisingApp\StudentDataModel\Contracts;

use AdvisingApp\StudentDataModel\Jobs\LookupPhoneNumber;
use AdvisingApp\StudentDataModel\Models\PhoneNumberLookup;
use InvalidArgumentException;
use Throwable;

interface PhoneNumberLookupService
{
    /**
     * Look up a phone number and return its persisted lookup record.
     *
     * Implementations must:
     *  - Validate that $phoneNumber is in E.164 format, throwing an
     *    {@see InvalidArgumentException} when it is not.
     *  - Return the existing {@see PhoneNumberLookup} when one already exists
     *    for the number, without calling the provider again (cost control).
     *  - Otherwise perform the provider lookup, persist the result, and
     *    return it.
     *
     * Definitive outcomes (a successful lookup, or a number the provider
     * cannot recognize) are persisted and returned. Operational errors that
     * may be transient (auth, rate limiting, server/connection failures) are
     * thrown so the caller (the queued {@see LookupPhoneNumber}
     * job) can retry them.
     *
     * @throws InvalidArgumentException when $phoneNumber is not a valid E.164 number.
     * @throws Throwable on a transient provider/API failure.
     */
    public function lookup(string $phoneNumber): PhoneNumberLookup;
}
