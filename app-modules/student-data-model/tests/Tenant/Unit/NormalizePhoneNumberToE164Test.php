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

use AdvisingApp\StudentDataModel\Actions\NormalizePhoneNumberToE164;

it('returns an already-E.164 number unchanged', function () {
    expect((new NormalizePhoneNumberToE164())('+16502530000'))->toBe('+16502530000');
});

it('strips formatting characters from an E.164 number', function () {
    expect((new NormalizePhoneNumberToE164())('+1 (650) 253-0000'))->toBe('+16502530000');
});

it('throws when the number has no country code', function () {
    (new NormalizePhoneNumberToE164())('6502530000');
})->throws(InvalidArgumentException::class);

it('throws when the number is not a valid phone number', function () {
    (new NormalizePhoneNumberToE164())('+1123');
})->throws(InvalidArgumentException::class);

it('throws when the value is not a phone number at all', function () {
    (new NormalizePhoneNumberToE164())('not a phone number');
})->throws(InvalidArgumentException::class);
