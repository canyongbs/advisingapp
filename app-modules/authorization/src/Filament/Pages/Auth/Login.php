<?php

namespace Assist\Authorization\Filament\Pages\Auth;

use Filament\Actions\Action;
use Filament\Pages\Auth\Login as FilamentLogin;

class Login extends FilamentLogin
{
    protected static string $view = 'authorization::login';

    protected function getSsoFormActions(): array
    {
        $ssoActions = [];

        if (! empty(config('services.azure.client_id'))) {
            $ssoActions[] = Action::make('azure_sso')
                ->label(__('Login with Azure SSO'))
                ->url(route('socialite.redirect', ['provider' => 'azure']))
                ->color('gray')
                ->icon('icon-microsoft')
                ->size('sm');
        }

        if (! empty(config('services.google.client_id'))) {
            $ssoActions[] = Action::make('google_sso')
                ->label(__('Login with Google SSO'))
                ->url(route('socialite.redirect', ['provider' => 'google']))
                ->icon('icon-google')
                ->color('gray')
                ->size('sm');
        }

        return $ssoActions;
    }
}
