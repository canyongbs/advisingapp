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

use AdvisingApp\StudentDataModel\Contracts\PhoneNumberLookupService;
use AdvisingApp\StudentDataModel\Enums\PhoneNumberLookupStatus;
use AdvisingApp\StudentDataModel\Jobs\LookupPhoneNumber;
use AdvisingApp\StudentDataModel\Models\PhoneNumberLookup;
use Illuminate\Support\Facades\Exceptions;

it('delegates to the phone number lookup service when the provider is configured', function () {
    $service = Mockery::mock(PhoneNumberLookupService::class);
    $service->shouldReceive('isConfigured')->andReturn(true);
    $service->shouldReceive('lookup')->once()->with('+16502530000'); // @phpstan-ignore method.notFound

    app()->instance(PhoneNumberLookupService::class, $service);

    app()->call([new LookupPhoneNumber('+16502530000'), 'handle']);
});

it('does nothing when the lookup provider is not configured', function () {
    $service = Mockery::mock(PhoneNumberLookupService::class);
    $service->shouldReceive('isConfigured')->andReturn(false);
    $service->shouldReceive('lookup')->never(); // @phpstan-ignore method.notFound

    app()->instance(PhoneNumberLookupService::class, $service);

    app()->call([new LookupPhoneNumber('+16502530000'), 'handle']);
});

it('records an invalid result when the number fails E.164 validation', function () {
    $service = Mockery::mock(PhoneNumberLookupService::class);
    $service->shouldReceive('isConfigured')->andReturn(true);
    $service->shouldReceive('lookup') // @phpstan-ignore method.notFound
        ->andThrow(new InvalidArgumentException('The phone number [+11234567890] is not a valid phone number.'));

    app()->instance(PhoneNumberLookupService::class, $service);

    app()->call([new LookupPhoneNumber('+11234567890'), 'handle']);

    expect(PhoneNumberLookup::query()->where('number', '+11234567890')->sole()->status)
        ->toBe(PhoneNumberLookupStatus::Invalid);
});

it('is uniquely identified by its phone number', function () {
    expect((new LookupPhoneNumber('+16502530000'))->uniqueId())->toBe('+16502530000');
});

it('records a lookup_failed result when the job ultimately fails', function () {
    Exceptions::fake();

    (new LookupPhoneNumber('+16502530000'))->failed(new Exception('Telnyx is unavailable'));

    $lookup = PhoneNumberLookup::query()->where('number', '+16502530000')->sole();

    expect($lookup->status)->toBe(PhoneNumberLookupStatus::LookupFailed)
        ->and($lookup->raw_response)->toBe(['error' => 'Telnyx is unavailable']);
});

it('does not overwrite an existing result when the job fails', function () {
    Exceptions::fake();

    PhoneNumberLookup::factory()->mobile()->create(['number' => '+16502530000']);

    (new LookupPhoneNumber('+16502530000'))->failed(new Exception('Telnyx is unavailable'));

    expect(PhoneNumberLookup::query()->where('number', '+16502530000')->count())->toBe(1)
        ->and(PhoneNumberLookup::query()->where('number', '+16502530000')->sole()->status)
        ->toBe(PhoneNumberLookupStatus::ValidMobile);
});
