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

use AdvisingApp\Authorization\Listeners\RememberLastLoginEmail;
use App\Models\User;
use Illuminate\Auth\Events\Login;
use Illuminate\Support\Facades\Cookie;

it('queues the remembered email cookie for the user who logged in', function (): void {
    $user = User::factory()->create(['email' => 'user@example.com']);

    app(RememberLastLoginEmail::class)(new Login('web', $user, false));

    $cookie = Cookie::queued(RememberLastLoginEmail::COOKIE_NAME);

    expect($cookie?->getValue())->toBe('user@example.com')
        ->and($cookie?->isHttpOnly())->toBeTrue();
});

it('replaces the remembered email when a different user logs in', function (): void {
    $firstUser = User::factory()->create(['email' => 'first@example.com']);
    $secondUser = User::factory()->create(['email' => 'second@example.com']);

    $listener = app(RememberLastLoginEmail::class);
    $listener(new Login('web', $firstUser, false));
    $listener(new Login('web', $secondUser, false));

    expect(Cookie::queued(RememberLastLoginEmail::COOKIE_NAME)?->getValue())->toBe('second@example.com');
});
