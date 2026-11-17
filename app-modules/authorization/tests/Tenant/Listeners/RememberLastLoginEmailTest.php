<?php

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
