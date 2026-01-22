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
      of the licensor in the software. Any use of the licensor's trademarks is subject
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
use AdvisingApp\Prospect\Models\ProspectEmailAddress;
use AdvisingApp\StudentDataModel\Models\BouncedEmailAddress;
use AdvisingApp\StudentDataModel\Models\EmailAddressOptInOptOut;

use function PHPUnit\Framework\assertFalse;
use function PHPUnit\Framework\assertTrue;

it('returns true when prospect has a healthy primary email address', function () {
    $prospect = Prospect::factory()->create();

    $emailAddress = ProspectEmailAddress::factory()
        ->for($prospect)
        ->create(['address' => 'healthy@example.com']);

    $prospect->primaryEmailAddress()->associate($emailAddress);
    $prospect->save();
    $prospect->refresh();

    assertTrue($prospect->canReceiveEmail());
});

it('returns false when prospect has no primary email address', function () {
    $prospect = Prospect::factory()->create();

    // Remove the primary email that was auto-created by the factory
    $prospect->primary_email_id = null;
    $prospect->save();
    $prospect->refresh();

    assertFalse($prospect->canReceiveEmail());
});

it('returns false when prospect primary email address is bounced', function () {
    $prospect = Prospect::factory()->create();

    $emailAddress = ProspectEmailAddress::factory()
        ->for($prospect)
        ->create(['address' => 'bounced@example.com']);

    BouncedEmailAddress::factory()->create([
        'address' => $emailAddress->address,
    ]);

    $prospect->primaryEmailAddress()->associate($emailAddress);
    $prospect->save();
    $prospect->refresh();

    assertFalse($prospect->canReceiveEmail());
});

it('returns false when prospect primary email address is opted out', function () {
    $prospect = Prospect::factory()->create();

    $emailAddress = ProspectEmailAddress::factory()
        ->for($prospect)
        ->create(['address' => 'optedout@example.com']);

    EmailAddressOptInOptOut::factory()
        ->optedOut()
        ->create(['address' => $emailAddress->address]);

    $prospect->primaryEmailAddress()->associate($emailAddress);
    $prospect->save();
    $prospect->refresh();

    assertFalse($prospect->canReceiveEmail());
});

it('returns true when prospect primary email address is opted in', function () {
    $prospect = Prospect::factory()->create();

    $emailAddress = ProspectEmailAddress::factory()
        ->for($prospect)
        ->create(['address' => 'optedin@example.com']);

    EmailAddressOptInOptOut::factory()
        ->optedIn()
        ->create(['address' => $emailAddress->address]);

    $prospect->primaryEmailAddress()->associate($emailAddress);
    $prospect->save();
    $prospect->refresh();

    assertTrue($prospect->canReceiveEmail());
});

it('returns false when prospect primary email is both bounced and opted out', function () {
    $prospect = Prospect::factory()->create();

    $emailAddress = ProspectEmailAddress::factory()
        ->for($prospect)
        ->create(['address' => 'both@example.com']);

    BouncedEmailAddress::factory()->create([
        'address' => $emailAddress->address,
    ]);

    EmailAddressOptInOptOut::factory()
        ->optedOut()
        ->create(['address' => $emailAddress->address]);

    $prospect->primaryEmailAddress()->associate($emailAddress);
    $prospect->save();
    $prospect->refresh();

    assertFalse($prospect->canReceiveEmail());
});
