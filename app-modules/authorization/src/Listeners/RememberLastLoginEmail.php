<?php

namespace AdvisingApp\Authorization\Listeners;

use App\Models\User;
use Illuminate\Auth\Events\Login;
use Illuminate\Support\Facades\Cookie;

class RememberLastLoginEmail
{
    public const string COOKIE_NAME = 'remembered_login_email';

    private const int COOKIE_LIFETIME_MINUTES = 60 * 24 * 365;

    public function __invoke(Login $event): void
    {
        $user = $event->user;

        if(! $user instanceof User) {
            return;
        }

        Cookie::queue(Cookie::make(
            self::COOKIE_NAME,
            $user->email,
            self::COOKIE_LIFETIME_MINUTES,
        ));
    }
}