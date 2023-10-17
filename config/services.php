<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
        'scheme' => 'https',
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    // TODO: Find a way to move this setting to the Auth Module
    'azure' => [
        'client_id' => env('AZURE_CLIENT_ID'),
        'client_secret' => env('AZURE_CLIENT_SECRET'),
        'redirect' => env('AZURE_REDIRECT_URI'),
        'tenant_id' => env('AZURE_TENANT_ID', 'common'),
    ],

    'google' => [
        'client_id' => env('GOOGLE_CLIENT_ID'),
        'client_secret' => env('GOOGLE_CLIENT_SECRET'),
        'redirect' => env('GOOGLE_REDIRECT_URI'),
    ],

    // TODO Find a way to move this setting to the Integration Twilio module
    'twilio' => [
        'account_sid' => env('TWILIO_ACCOUNT_SID'),
        'auth_token' => env('TWILIO_AUTH_TOKEN'),
        'from_number' => env('TWILIO_FROM_NUMBER'),
        'test_to_number' => env('TWILIO_TEST_TO_NUMBER', null),
        'enable_test_sender' => env('TWILIO_ENABLE_TEST_SENDER', false),
    ],

    // TODO Find a way to move this setting to the Integration AI module
    'azure_open_ai' => [
        'endpoint' => env('AZURE_OPEN_AI_BASE_ENDPOINT'),
        'api_key' => env('AZURE_OPEN_AI_API_KEY'),
        'api_version' => env('AZURE_OPEN_AI_API_VERSION'),
        'deployment_name' => env('AZURE_OPEN_AI_DEPLOYMENT_NAME'),
        'enable_test_mode' => env('AZURE_OPEN_AI_ENABLE_TEST_MODE', true),
    ],

    'microsoft_graph' => [
        'client_id' => env('MICROSOFT_GRAPH_CLIENT_ID'),
        'client_secret' => env('MICROSOFT_GRAPH_CLIENT_SECRET'),
        'redirect' => env('MICROSOFT_GRAPH_REDIRECT_URI'),
        'scopes' => env('MICROSOFT_GRAPH_SCOPES', 'user.read calendars.readwrite calendars.read calendars.read.shared calendars.readbasic calendars.readwrite.shared'),
        'authority' => env('MICROSOFT_GRAPH_AUTHORITY', 'https://login.microsoftonline.com/common'),
        'authorize_endpoint' => env('MICROSOFT_GRAPH_AUTHORIZE_ENDPOINT', '/oauth2/v2.0/authorize'),
        'token_endpoint' => env('MICROSOFT_GRAPH_TOKEN_ENDPOINT', '/oauth2/v2.0/token'),
    ],

    'google_calendar' => [
        'client_id' => env('GOOGLE_CALENDAR_CLIENT_ID'),
        'client_secret' => env('GOOGLE_CALENDAR_CLIENT_SECRET'),
        'redirect' => env('GOOGLE_CALENDAR_REDIRECT_URI'),
    ],
];
