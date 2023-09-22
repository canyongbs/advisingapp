<?php

namespace Assist\Authorization\Http\Responses\Auth;

use Filament\Facades\Filament;
use Illuminate\Http\RedirectResponse;
use Filament\Http\Responses\Auth\LogoutResponse;
use Assist\Authorization\Enums\SocialiteProvider;

class SocialiteLogoutResponse extends LogoutResponse
{
    public function __construct(
        public SocialiteProvider $provider
    ) {}

    public function toResponse($request): RedirectResponse
    {
        return redirect(
            $this->provider->driver()
                ->getLogoutUrl(Filament::hasLogin() ? Filament::getLoginUrl() : Filament::getUrl())
        );
    }
}
