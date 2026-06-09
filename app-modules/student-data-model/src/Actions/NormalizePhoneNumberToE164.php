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

namespace AdvisingApp\StudentDataModel\Actions;

use InvalidArgumentException;
use libphonenumber\NumberParseException;
use libphonenumber\PhoneNumberFormat;
use libphonenumber\PhoneNumberUtil;

class NormalizePhoneNumberToE164
{
    /**
     * Normalize a phone number to E.164 format.
     *
     * Numbers entering the system (SIS sync, manual entry, imports) should
     * already be E.164. This acts as a defense-in-depth check so any current
     * or future caller can re-validate the format before it is used.
     *
     * @throws InvalidArgumentException when the value cannot be parsed as a valid phone number.
     */
    public function __invoke(string $phoneNumber): string
    {
        $phoneNumberUtil = PhoneNumberUtil::getInstance();

        try {
            // Parse without a region: this only succeeds when the number is
            // already in E.164 format (i.e. it carries its own country code).
            $parsed = $phoneNumberUtil->parse($phoneNumber);
        } catch (NumberParseException $exception) {
            throw new InvalidArgumentException(
                "The phone number [{$phoneNumber}] could not be parsed as a valid E.164 number.",
                previous: $exception,
            );
        }

        if (! $phoneNumberUtil->isValidNumber($parsed)) {
            throw new InvalidArgumentException(
                "The phone number [{$phoneNumber}] is not a valid phone number.",
            );
        }

        return $phoneNumberUtil->format($parsed, PhoneNumberFormat::E164);
    }
}
