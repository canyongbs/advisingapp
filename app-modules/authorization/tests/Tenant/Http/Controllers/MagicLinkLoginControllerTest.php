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

use AdvisingApp\Authorization\Models\LoginMagicLink;
use App\Models\User;
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

it('rejects a magic link with an invalid code', function () {
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
                        'code' => 'abc123',
                        'user_id' => $magicLink->user_id,
                    ]
                )
            ),
        ],
    ))
        ->assertForbidden();

    assertGuest($panel->getAuthGuard());
});

it('rejects a magic link with a non-matching user ID', function () {
    $code = Str::random();

    $magicLink = LoginMagicLink::factory()->withCode($code)->create();

    $panel = Filament::getPanel('admin');

    $secondUser = User::factory()->create();

    get(URL::temporarySignedRoute(
        name: 'magic-link.login',
        expiration: now()->addMinutes(10)->toImmutable(),
        parameters: [
            'magicLink' => $magicLink->getKey(),
            'payload' => urlencode(
                Crypt::encrypt(
                    [
                        'code' => $code,
                        'user_id' => $secondUser->getKey(),
                    ]
                )
            ),
        ],
    ))
        ->assertForbidden();

    assertGuest($panel->getAuthGuard());
});

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
