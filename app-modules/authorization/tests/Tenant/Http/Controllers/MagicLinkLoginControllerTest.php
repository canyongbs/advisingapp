<?php

use AdvisingApp\Authorization\Models\LoginMagicLink;
use Filament\Facades\Filament;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;

use function Pest\Laravel\assertAuthenticatedAs;
use function Pest\Laravel\assertGuest;
use function Pest\Laravel\get;

it('requires a valid signed URL ', function () {
    $code = Str::random();

    $magicLink = LoginMagicLink::factory()->withCode($code)->create();

    get(route('magic-link.login', [
        'magicLink' => $magicLink->id,
        'payload' => urlencode(Crypt::encrypt([
            'code' => $code,
            'user_id' => $magicLink->user_id,
        ])),
    ]))
        ->assertForbidden();
});

it('rejects a magic link that is older than 15 minutes', function () {
    $code = Str::random();

    $magicLink = LoginMagicLink::factory()->withCode($code)->create(
        [
            'created_at' => now()->subMinutes(16),
        ]
    );

    $panel = Filament::getPanel('admin');

    get(URL::temporarySignedRoute(
        name: 'magic-link.login',
        expiration: now()->addMinutes(10)->toImmutable(),
        parameters: [
            'magicLink' => $magicLink->getKey(),
            'payload' => urlencode(
                Crypt::encrypt(
                    [
                        'code' => $code,
                        'user_id' => $magicLink->user_id,
                    ]
                )
            ),
        ],
    ))
        ->assertForbidden();

    assertGuest($panel->getAuthGuard());
});

it('rejects a magic link that has already been used', function () {
    $code = Str::random();

    $magicLink = LoginMagicLink::factory()->withCode($code)->used()->create();

    $panel = Filament::getPanel('admin');

    get(URL::temporarySignedRoute(
        name: 'magic-link.login',
        expiration: now()->addMinutes(10)->toImmutable(),
        parameters: [
            'magicLink' => $magicLink->getKey(),
            'payload' => urlencode(
                Crypt::encrypt(
                    [
                        'code' => $code,
                        'user_id' => $magicLink->user_id,
                    ]
                )
            ),
        ],
    ))
        ->assertForbidden();

    assertGuest($panel->getAuthGuard());
});

//it('rejects a magic link with an invalid code', function () {});
//
//it('rejects a magic link with a non-matching user ID', function () {});

it('logs in the user and redirects to the admin panel home', function () {
    $code = Str::random();

    $magicLink = LoginMagicLink::factory()->withCode($code)->create();

    $panel = Filament::getPanel('admin');

    get(URL::temporarySignedRoute(
        name: 'magic-link.login',
        expiration: now()->addMinutes(10)->toImmutable(),
        parameters: [
            'magicLink' => $magicLink->getKey(),
            'payload' => urlencode(
                Crypt::encrypt(
                    [
                        'code' => $code,
                        'user_id' => $magicLink->user_id,
                    ]
                )
            ),
        ],
    ))
        ->assertRedirect($panel->getHomeUrl());

    assertAuthenticatedAs($magicLink->user, $panel->getAuthGuard());

    $magicLink->refresh();

    expect($magicLink->used_at)->not->toBeNull();
});
