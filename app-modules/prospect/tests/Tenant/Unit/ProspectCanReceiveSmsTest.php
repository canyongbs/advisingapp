<?php

/*
<COPYRIGHT>

    Copyright © 2016-2026, Canyon GBS LLC. All rights reserved.

    Advising App™ is licensed under the Elastic License 2.0. For more details,
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
    - Canyon GBS LLC respects the intellectual property rights of others and expects the
      same in return. Canyon GBS™ and Advising App™ are registered trademarks of
      Canyon GBS LLC, and we are committed to enforcing and protecting our trademarks
      vigorously.
    - The software solution, including services, infrastructure, and code, is offered as a
      Software as a Service (SaaS) by Canyon GBS LLC.
    - Use of this software implies agreement to the license terms and conditions as stated
      in the Elastic License 2.0.

    For more information or inquiries please visit our website at
    https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

use AdvisingApp\Prospect\Models\Prospect;
use AdvisingApp\Prospect\Models\ProspectPhoneNumber;
use AdvisingApp\StudentDataModel\Models\BouncedPhoneNumber;
use AdvisingApp\StudentDataModel\Models\SmsOptOutPhoneNumber;

use function PHPUnit\Framework\assertFalse;
use function PHPUnit\Framework\assertTrue;

it('returns true when prospect has a healthy primary phone number', function () {
    $prospect = Prospect::factory()->create();

    $phoneNumber = ProspectPhoneNumber::factory()
        ->for($prospect)
        ->create(['can_receive_sms' => true]);

    $prospect->primaryPhoneNumber()->associate($phoneNumber);
    $prospect->save();
    $prospect->refresh();

    assertTrue($prospect->canReceiveSms());
});

it('returns false when prospect has no primary phone number', function () {
    $prospect = Prospect::factory()->create();

    // Remove the primary phone that was auto-created if applicable
    $prospect->primary_phone_id = null;
    $prospect->save();
    $prospect->refresh();

    assertFalse($prospect->canReceiveSms());
});

it('returns false when prospect primary phone number has can_receive_sms disabled', function () {
    $prospect = Prospect::factory()->create();

    $phoneNumber = ProspectPhoneNumber::factory()
        ->for($prospect)
        ->create(['can_receive_sms' => false]);

    $prospect->primaryPhoneNumber()->associate($phoneNumber);
    $prospect->save();
    $prospect->refresh();

    assertFalse($prospect->canReceiveSms());
});

it('returns false when prospect primary phone number is sms opted out', function () {
    $prospect = Prospect::factory()->create();

    $phoneNumber = ProspectPhoneNumber::factory()
        ->for($prospect)
        ->create(['can_receive_sms' => true]);

    SmsOptOutPhoneNumber::factory()->create([
        'number' => $phoneNumber->number,
    ]);

    $prospect->primaryPhoneNumber()->associate($phoneNumber);
    $prospect->save();
    $prospect->refresh();

    assertFalse($prospect->canReceiveSms());
});

it('returns false when prospect primary phone number is bounced', function () {
    $prospect = Prospect::factory()->create();

    $phoneNumber = ProspectPhoneNumber::factory()
        ->for($prospect)
        ->create(['can_receive_sms' => true]);

    BouncedPhoneNumber::factory()->create([
        'number' => $phoneNumber->number,
    ]);

    $prospect->primaryPhoneNumber()->associate($phoneNumber);
    $prospect->save();
    $prospect->refresh();

    assertFalse($prospect->canReceiveSms());
});

it('returns false when prospect primary phone is both bounced and sms opted out', function () {
    $prospect = Prospect::factory()->create();

    $phoneNumber = ProspectPhoneNumber::factory()
        ->for($prospect)
        ->create(['can_receive_sms' => true]);

    BouncedPhoneNumber::factory()->create([
        'number' => $phoneNumber->number,
    ]);

    SmsOptOutPhoneNumber::factory()->create([
        'number' => $phoneNumber->number,
    ]);

    $prospect->primaryPhoneNumber()->associate($phoneNumber);
    $prospect->save();
    $prospect->refresh();

    assertFalse($prospect->canReceiveSms());
});
