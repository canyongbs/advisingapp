<?php

use Microsoft\Graph\Graph;
use Microsoft\Graph\Model\User;
use Illuminate\Support\Facades\Http;
use League\OAuth2\Client\Provider\GenericProvider;

Route::middleware('web')
    ->prefix('google/calendar')
    ->name('google.calendar.')
    ->group(function () {
        Route::get('/login', function () {
            $client = new \Google\Client();

            $client->setScopes([
                \Google\Service\Calendar::CALENDAR,
            ]);

            $client->setAuthConfig(config()->get('google-calendar.auth_profiles.oauth.credentials_json'));

            return redirect()->away($client->createAuthUrl());
        })->name('login');

        Route::get('/callback', function (Request $request) {
            dd($request);
        });
    });

Route::middleware('web')
    ->prefix('microsoft/graph')
    ->name('microsoft.graph.')
    ->group(function () {
        Route::get('/login', function () {
            // Initialize the OAuth client
            $oauthClient = new GenericProvider([
                'clientId' => config('services.microsoft_graph.client_id'),
                'clientSecret' => config('services.microsoft_graph.client_secret'),
                'redirectUri' => config('services.microsoft_graph.redirect'),
                'urlAuthorize' => config('services.microsoft_graph.authority') . config('services.microsoft_graph.authorize_endpoint'),
                'urlAccessToken' => config('services.microsoft_graph.authority') . config('services.microsoft_graph.token_endpoint'),
                'urlResourceOwnerDetails' => '',
                'scopes' => config('services.microsoft_graph.scopes'),
            ]);

            $authUrl = $oauthClient->getAuthorizationUrl();

            // Save client state so we can validate in callback
            session(['oauthState' => $oauthClient->getState()]);

            // Redirect to AAD signin page
            return redirect()->away($authUrl);
            // $response = Http::asForm()->post('https://login.microsoftonline.com/af905c0d-24ca-4c1b-86e8-e6ac7d45c7f1/oauth2/v2.0/token', [
            //     'client_id' => config('services.microsoft_graph.client_id'),
            //     'client_secret' => config('services.microsoft_graph.client_secret'),
            //     'scope' => 'https://graph.microsoft.com/.default',
            //     'grant_type' => 'client_credentials',
            // ]);
            //
            // dd($response->json());

            // $token = '';
            //
            // $graph = new Graph();
            // $graph->setAccessToken($token);
            //
            // $user = $graph->createRequest('GET', '/me')
            //     ->setReturnType(User::class)
            //     ->execute();
            //
            // echo "Hello, I am {$user->getGivenName()}.";
        })->name('login');

        Route::get('/callback', function (Request $request) {
            dd($request);
        });
    });
