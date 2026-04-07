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

use AdvisingApp\Authorization\Settings\AzureSsoSettings;
use App\Health\Checks\AzureCredentialsExpiringCheck;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Spatie\Health\Enums\Status;

beforeEach(function () {
    Cache::tags(['azure_credentials_expiring'])->flush();

    $azureSsoSettings = app(AzureSsoSettings::class);
    $azureSsoSettings->tenant_id = 'test_tenant';
    $azureSsoSettings->client_id = 'test_client';
    $azureSsoSettings->client_secret = 'secret';
    $azureSsoSettings->save();
});

it('returns with ok status if credentials exist and do not expire soon', function () {
    $azureSsoSettings = app(AzureSsoSettings::class);

    Http::fake([
        'https://login.microsoftonline.com/' . $azureSsoSettings->tenant_id . '/oauth2/v2.0/token' => Http::response(['access_token' => 'token'], 200),
        "https://graph.microsoft.com/v1.0/applications(appId='{$azureSsoSettings->client_id}')" . '?$select=passwordCredentials' => Http::response([
            'passwordCredentials' => [
                (object) [
                    'endDateTime' => now()->addDays(60),
                    'hint' => 'sec',
                ],
            ],
        ], 200),
    ]);

    expect((new AzureCredentialsExpiringCheck())->run()->status)->toBe(Status::ok());
});

it('returns with ok status if there is an exception', function () {
    $azureSsoSettings = app(AzureSsoSettings::class);

    Http::fake([
        'https://login.microsoftonline.com/' . $azureSsoSettings->tenant_id . '/oauth2/v2.0/token' => Http::response(['access_token' => 'token'], 500),
    ]);

    expect((new AzureCredentialsExpiringCheck())->run()->status)->toBe(Status::ok());

    Http::fake([
        'https://login.microsoftonline.com/' . $azureSsoSettings->tenant_id . '/oauth2/v2.0/token' => Http::response(['access_token' => 'token'], 200),
        "https://graph.microsoft.com/v1.0/applications(appId='{$azureSsoSettings->client_id}')" . '?$select=passwordCredentials' => Http::response([
            'passwordCredentials' => [
                (object) [
                    'endDateTime' => now()->addDays(60),
                    'hint' => 'sec',
                ],
            ],
        ], 500),
    ]);

    expect((new AzureCredentialsExpiringCheck())->run()->status)->toBe(Status::ok());

    Http::fake([
        'https://login.microsoftonline.com/' . $azureSsoSettings->tenant_id . '/oauth2/v2.0/token' => Http::response(['access_token' => 'token'], 200),
        "https://graph.microsoft.com/v1.0/applications(appId='{$azureSsoSettings->client_id}')" . '?$select=passwordCredentials' => Http::response([
            'passwordCredentials' => [(object) []],
        ], 500),
    ]);

    expect((new AzureCredentialsExpiringCheck())->run()->status)->toBe(Status::ok());
});

it('uses the soonest ending credential if it receives multiple', function () {
    $azureSsoSettings = app(AzureSsoSettings::class);

    Http::fake([
        'https://login.microsoftonline.com/' . $azureSsoSettings->tenant_id . '/oauth2/v2.0/token' => Http::response(['access_token' => 'token'], 200),
        "https://graph.microsoft.com/v1.0/applications(appId='{$azureSsoSettings->client_id}')" . '?$select=passwordCredentials' => Http::response([
            'passwordCredentials' => [
                (object) [
                    'endDateTime' => now()->addDays(60),
                    'hint' => 'sec',
                ],
                (object) [
                    'endDateTime' => now()->addDays(30),
                    'hint' => 'sec',
                ],
            ],
        ], 200),
    ]);

    expect((new AzureCredentialsExpiringCheck())->run()->status)->toBe(Status::warning());
});

it('returns with a failed status if the credential has already expired', function () {
    $azureSsoSettings = app(AzureSsoSettings::class);

    Http::fake([
        'https://login.microsoftonline.com/' . $azureSsoSettings->tenant_id . '/oauth2/v2.0/token' => Http::response(['access_token' => 'token'], 200),
        "https://graph.microsoft.com/v1.0/applications(appId='{$azureSsoSettings->client_id}')" . '?$select=passwordCredentials' => Http::response([
            'passwordCredentials' => [
                (object) [
                    'endDateTime' => now()->subDays(5),
                    'hint' => 'sec',
                ],
            ],
        ], 200),
    ]);

    expect((new AzureCredentialsExpiringCheck())->run()->status)->toBe(Status::failed());
});

it('returns with a warning status if the credential is within 45 days of expiring', function () {
    $azureSsoSettings = app(AzureSsoSettings::class);

    Http::fake([
        'https://login.microsoftonline.com/' . $azureSsoSettings->tenant_id . '/oauth2/v2.0/token' => Http::response(['access_token' => 'token'], 200),
        "https://graph.microsoft.com/v1.0/applications(appId='{$azureSsoSettings->client_id}')" . '?$select=passwordCredentials' => Http::response([
            'passwordCredentials' => [
                (object) [
                    'endDateTime' => now()->addDays(30),
                    'hint' => 'sec',
                ],
            ],
        ], 200),
    ]);

    expect((new AzureCredentialsExpiringCheck())->run()->status)->toBe(Status::warning());
});
