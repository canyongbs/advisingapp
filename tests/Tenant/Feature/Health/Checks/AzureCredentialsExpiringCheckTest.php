<?php

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
                    'hint' => 'sec'
                    ]
            ]
        ], 200)
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
                    'hint' => 'sec'
                    ]
            ]
        ], 500)
    ]);

    expect((new AzureCredentialsExpiringCheck())->run()->status)->toBe(Status::ok());
    
    Http::fake([
        'https://login.microsoftonline.com/' . $azureSsoSettings->tenant_id . '/oauth2/v2.0/token' => Http::response(['access_token' => 'token'], 200),
        "https://graph.microsoft.com/v1.0/applications(appId='{$azureSsoSettings->client_id}')" . '?$select=passwordCredentials' => Http::response([
            'passwordCredentials' => [(object) []]
        ], 500)
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
                    'hint' => 'sec'
                    ],
                (object) [
                    'endDateTime' => now()->addDays(30),
                    'hint' => 'sec'
                    ],
            ]
        ], 200)
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
                    'hint' => 'sec'
                    ]
            ]
        ], 200)
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
                    'hint' => 'sec'
                    ]
            ]
        ], 200)
    ]);

    expect((new AzureCredentialsExpiringCheck())->run()->status)->toBe(Status::warning());
});