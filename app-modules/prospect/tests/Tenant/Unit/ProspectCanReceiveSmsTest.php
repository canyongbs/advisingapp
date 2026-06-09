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

use AdvisingApp\Prospect\Models\Prospect;
use AdvisingApp\StudentDataModel\Enums\PhoneNumberLookupStatus;
use AdvisingApp\StudentDataModel\Models\BouncedPhoneNumber;
use AdvisingApp\StudentDataModel\Models\SmsOptOutPhoneNumber;

use function PHPUnit\Framework\assertFalse;
use function PHPUnit\Framework\assertTrue;

it('returns true when the primary phone has a textable Telnyx lookup and is not opted out or bounced', function () {
    $prospect = Prospect::factory()->create();

    assertTrue($prospect->canReceiveSms());
});

it('returns false when the prospect has no primary phone number', function () {
    $prospect = Prospect::factory()->create();

    $prospect->primary_phone_id = null;
    $prospect->save();
    $prospect->refresh();

    assertFalse($prospect->canReceiveSms());
});

it('returns false when the primary phone has no Telnyx lookup yet', function () {
    $prospect = Prospect::factory()->create();

    // Simulate a phone created before the lookup job ran.
    $prospect->primaryPhoneNumber->phoneNumberLookup()->delete();
    $prospect->refresh();

    assertFalse($prospect->canReceiveSms());
});

it('returns false when the primary phone has a non-textable Telnyx lookup', function (PhoneNumberLookupStatus $status) {
    $prospect = Prospect::factory()->create();

    $prospect->primaryPhoneNumber->phoneNumberLookup->update(['status' => $status]);
    $prospect->refresh();

    assertFalse($prospect->canReceiveSms());
})->with([
    PhoneNumberLookupStatus::Invalid,
    PhoneNumberLookupStatus::ValidLandline,
    PhoneNumberLookupStatus::Unknown,
    PhoneNumberLookupStatus::LookupFailed,
]);

it('returns false when the primary phone is opted out of SMS', function () {
    $prospect = Prospect::factory()->create();

    SmsOptOutPhoneNumber::factory()->create([
        'number' => $prospect->primaryPhoneNumber->number,
    ]);

    assertFalse($prospect->canReceiveSms());
});

it('returns false when the primary phone has previously bounced', function () {
    $prospect = Prospect::factory()->create();

    BouncedPhoneNumber::factory()->create([
        'number' => $prospect->primaryPhoneNumber->number,
    ]);

    assertFalse($prospect->canReceiveSms());
});

it('returns false when the primary phone is both bounced and opted out', function () {
    $prospect = Prospect::factory()->create();
    $number = $prospect->primaryPhoneNumber->number;

    BouncedPhoneNumber::factory()->create(['number' => $number]);
    SmsOptOutPhoneNumber::factory()->create(['number' => $number]);

    assertFalse($prospect->canReceiveSms());
});
