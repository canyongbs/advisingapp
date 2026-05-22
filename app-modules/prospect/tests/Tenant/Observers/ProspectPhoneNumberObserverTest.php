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

use AdvisingApp\IntegrationTwilio\Settings\TwilioSettings;
use AdvisingApp\Prospect\Models\Prospect;
use AdvisingApp\Prospect\Models\ProspectPhoneNumber;
use AdvisingApp\StudentDataModel\Jobs\LookupPhoneNumber;
use AdvisingApp\StudentDataModel\Models\PhoneNumberLookup;
use Illuminate\Support\Facades\Bus;

beforeEach(function () {
    $settings = app(TwilioSettings::class);
    $settings->telnyx_api_key = 'test-telnyx-api-key';
    $settings->save();
});

it('queues a lookup when a prospect phone number is created', function () {
    Bus::fake([LookupPhoneNumber::class]);

    $prospect = Prospect::factory()->create();

    ProspectPhoneNumber::factory()->create([
        'prospect_id' => $prospect->getKey(),
        'number' => '+16502530000',
    ]);

    expect(Bus::dispatched(LookupPhoneNumber::class, fn (LookupPhoneNumber $job) => $job->phoneNumber === '+16502530000'))
        ->toHaveCount(1);
});

it('queues a lookup when the number on a prospect phone number changes', function () {
    Bus::fake([LookupPhoneNumber::class]);

    $prospect = Prospect::factory()->create();
    $phoneNumber = ProspectPhoneNumber::factory()->create([
        'prospect_id' => $prospect->getKey(),
        'number' => '+16502530000',
    ]);

    $phoneNumber->update(['number' => '+16502531111']);

    expect(Bus::dispatched(LookupPhoneNumber::class, fn (LookupPhoneNumber $job) => $job->phoneNumber === '+16502531111'))
        ->toHaveCount(1);
});

it('does not queue a lookup when a non-number field changes', function () {
    Bus::fake([LookupPhoneNumber::class]);

    $prospect = Prospect::factory()->create();
    $phoneNumber = ProspectPhoneNumber::factory()->create([
        'prospect_id' => $prospect->getKey(),
        'number' => '+16502530000',
    ]);

    $phoneNumber->update(['type' => 'Work']);

    expect(Bus::dispatched(LookupPhoneNumber::class, fn (LookupPhoneNumber $job) => $job->phoneNumber === '+16502530000'))
        ->toHaveCount(1);
});

it('does not queue a lookup when a result already exists for the number', function () {
    Bus::fake([LookupPhoneNumber::class]);

    $prospect = Prospect::factory()->create();
    PhoneNumberLookup::factory()->create(['number' => '+16502530000']);

    ProspectPhoneNumber::factory()->create([
        'prospect_id' => $prospect->getKey(),
        'number' => '+16502530000',
    ]);

    expect(Bus::dispatched(LookupPhoneNumber::class, fn (LookupPhoneNumber $job) => $job->phoneNumber === '+16502530000'))
        ->toHaveCount(0);
});

it('does not queue a lookup when no lookup provider is configured', function () {
    $settings = app(TwilioSettings::class);
    $settings->telnyx_api_key = null;
    $settings->save();

    Bus::fake([LookupPhoneNumber::class]);

    $prospect = Prospect::factory()->create();

    ProspectPhoneNumber::factory()->create([
        'prospect_id' => $prospect->getKey(),
        'number' => '+16502530000',
    ]);

    expect(Bus::dispatched(LookupPhoneNumber::class, fn (LookupPhoneNumber $job) => $job->phoneNumber === '+16502530000'))
        ->toHaveCount(0);
});
