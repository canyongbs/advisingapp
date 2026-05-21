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
use AdvisingApp\IntegrationTwilio\Tests\Fixtures\FakeTelnyxHttpClient;
use AdvisingApp\StudentDataModel\Contracts\PhoneNumberLookupService;
use AdvisingApp\StudentDataModel\Enums\PhoneNumberLookupStatus;
use AdvisingApp\StudentDataModel\Models\PhoneNumberLookup;
use Telnyx\ApiRequestor;
use Telnyx\Exception\ApiErrorException;
use Telnyx\HttpClient\CurlClient;

/**
 * @param array<mixed> $data
 */
function fakeTelnyxLookup(array $data, int $status = 200): void
{
    ApiRequestor::setHttpClient(new FakeTelnyxHttpClient(json_encode($data, JSON_THROW_ON_ERROR), $status));
}

beforeEach(function () {
    $settings = app(TwilioSettings::class);
    $settings->telnyx_api_key = 'test-telnyx-api-key';
    $settings->save();
});

afterEach(function () {
    // Restore the SDK's default HTTP client so the stub does not leak into other tests.
    ApiRequestor::setHttpClient(CurlClient::instance());
});

it('stores a successful carrier lookup result', function () {
    fakeTelnyxLookup([
        'data' => [
            'record_type' => 'number_lookup',
            'phone_number' => '+16502530000',
            'country_code' => 'US',
            'carrier' => [
                'name' => 'Acme Wireless',
                'type' => 'mobile',
                'error_code' => null,
            ],
        ],
    ]);

    $lookup = app(PhoneNumberLookupService::class)->lookup('+16502530000');

    expect($lookup->number)->toBe('+16502530000')
        ->and($lookup->status)->toBe(PhoneNumberLookupStatus::ValidMobile)
        ->and($lookup->carrier_name)->toBe('Acme Wireless')
        ->and($lookup->carrier_type)->toBe('mobile')
        ->and($lookup->raw_response)->toHaveKey('data')
        ->and(PhoneNumberLookup::query()->where('number', '+16502530000')->count())->toBe(1);
});

it('maps a successful lookup with an inconclusive carrier type to unknown', function () {
    fakeTelnyxLookup([
        'data' => [
            'record_type' => 'number_lookup',
            'phone_number' => '+16502530000',
            'carrier' => ['name' => 'Some Carrier', 'type' => 'voicemail'],
        ],
    ]);

    expect(app(PhoneNumberLookupService::class)->lookup('+16502530000')->status)
        ->toBe(PhoneNumberLookupStatus::Unknown);
});

it('falls back to portability data when the carrier object is null', function () {
    // Telnyx commonly returns a null carrier for US / VoIP / ported numbers,
    // with the line type and carrier name in the portability object instead.
    fakeTelnyxLookup([
        'data' => [
            'record_type' => 'number_lookup',
            'phone_number' => '+16502530000',
            'valid_number' => true,
            'carrier' => null,
            'portability' => [
                'line_type' => 'voip',
                'spid_carrier_name' => 'Bandwidth.com CLEC, LLC',
            ],
        ],
    ]);

    $lookup = app(PhoneNumberLookupService::class)->lookup('+16502530000');

    expect($lookup->status)->toBe(PhoneNumberLookupStatus::ValidVoip)
        ->and($lookup->carrier_type)->toBe('voip')
        ->and($lookup->carrier_name)->toBe('Bandwidth.com CLEC, LLC');
});

it('maps a number Telnyx reports as not valid to invalid', function () {
    fakeTelnyxLookup([
        'data' => [
            'record_type' => 'number_lookup',
            'phone_number' => '+16502530000',
            'valid_number' => false,
            'carrier' => null,
        ],
    ]);

    expect(app(PhoneNumberLookupService::class)->lookup('+16502530000')->status)
        ->toBe(PhoneNumberLookupStatus::Invalid);
});

it('reuses an existing lookup result instead of calling Telnyx again', function () {
    $existing = PhoneNumberLookup::factory()->mobile()->create(['number' => '+16502530000']);

    // A different result that would be returned if Telnyx were (incorrectly) called.
    fakeTelnyxLookup([
        'data' => [
            'record_type' => 'number_lookup',
            'carrier' => ['name' => 'Other Carrier', 'type' => 'voip'],
        ],
    ]);

    $lookup = app(PhoneNumberLookupService::class)->lookup('+16502530000');

    expect($lookup->is($existing))->toBeTrue()
        ->and($lookup->status)->toBe(PhoneNumberLookupStatus::ValidMobile)
        ->and(PhoneNumberLookup::query()->where('number', '+16502530000')->count())->toBe(1);
});

it('stores an invalid result when Telnyx cannot recognize the number', function () {
    fakeTelnyxLookup([
        'errors' => [
            ['code' => '10005', 'title' => 'Resource not found'],
        ],
    ], status: 404);

    $lookup = app(PhoneNumberLookupService::class)->lookup('+16502530000');

    expect($lookup->status)->toBe(PhoneNumberLookupStatus::Invalid)
        ->and($lookup->carrier_name)->toBeNull()
        ->and($lookup->carrier_type)->toBeNull();
});

it('re-throws transient provider errors without storing a result', function () {
    fakeTelnyxLookup([
        'errors' => [
            ['code' => '90000', 'title' => 'Internal server error'],
        ],
    ], status: 500);

    expect(fn () => app(PhoneNumberLookupService::class)->lookup('+16502530000'))
        ->toThrow(ApiErrorException::class);

    expect(PhoneNumberLookup::query()->where('number', '+16502530000')->count())->toBe(0);
});

it('throws when the phone number is not a valid E.164 number', function () {
    expect(fn () => app(PhoneNumberLookupService::class)->lookup('not-a-phone-number'))
        ->toThrow(InvalidArgumentException::class);
});

it('reports as configured when the Telnyx API key is set', function () {
    expect(app(PhoneNumberLookupService::class)->isConfigured())->toBeTrue();
});

it('reports as not configured when the Telnyx API key is blank', function () {
    $settings = app(TwilioSettings::class);
    $settings->telnyx_api_key = null;
    $settings->save();

    expect(app(PhoneNumberLookupService::class)->isConfigured())->toBeFalse();
});
