<?php

namespace Assist\Authorization\Http\Controllers\Auth;

use Filament\Facades\Filament;
use Assist\Authorization\Enums\SocialiteProvider;
use Filament\Http\Responses\Auth\Contracts\LogoutResponse;
use Assist\Authorization\Http\Responses\Auth\SocialiteLogoutResponse;
use Filament\Http\Controllers\Auth\LogoutController as FilamentLogoutController;

class LogoutController extends FilamentLogoutController
{
    public function __invoke(): LogoutResponse
    {
        /** @var SocialiteProvider|null $socialiteProvider */
        $socialiteProvider = session('auth_via');

        Filament::auth()->logout();

        session()->invalidate();
        session()->regenerateToken();

        return match ($socialiteProvider?->value) {
            'azure' => app(SocialiteLogoutResponse::class, [
                'provider' => $socialiteProvider,
            ]),
            default => app(LogoutResponse::class),
        };
    }
}
