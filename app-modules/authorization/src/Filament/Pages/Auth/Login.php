<?php

/*
<COPYRIGHT>

    Copyright © 2016-2024, Canyon GBS LLC. All rights reserved.

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

namespace AdvisingApp\Authorization\Filament\Pages\Auth;

use App\Models\User;
use Filament\Actions\Action;
use Filament\Facades\Filament;
use Filament\Forms\Components\TextInput;
use Filament\Models\Contracts\FilamentUser;
use Illuminate\Validation\ValidationException;
use Filament\Pages\Auth\Login as FilamentLogin;
use AdvisingApp\Authorization\Settings\AzureSsoSettings;
use AdvisingApp\Authorization\Settings\GoogleSsoSettings;
use Filament\Http\Responses\Auth\Contracts\LoginResponse;
use AdvisingApp\MultifactorAuthentication\Services\MultifactorService;
use DanHarrin\LivewireRateLimiting\Exceptions\TooManyRequestsException;

class Login extends FilamentLogin
{
    protected static string $view = 'authorization::login';

    protected $needsMFA = false;

    public function authenticate(): ?LoginResponse
    {
        try {
            $this->rateLimit(5);
        } catch (TooManyRequestsException $exception) {
            $this->getRateLimitedNotification($exception)?->send();

            return null;
        }

        $data = $this->form->getState();

        if (! Filament::auth()->once($this->getCredentialsFromFormData($data), $data['remember'] ?? false)) {
            $this->throwFailureValidationException();
        }

        /** @var User $user */
        $user = Filament::auth()->user();

        if (
            ($user instanceof FilamentUser) &&
            (! $user->canAccessPanel(Filament::getCurrentPanel()))
        ) {
            Filament::auth()->logout();

            $this->throwFailureValidationException();
        }

        if ($user->hasConfirmedTwoFactor()) {
            $this->needsMFA = true;

            if (empty($data['code'])) {
                Filament::auth()->logout();

                return null;
            }

            if (! app(MultifactorService::class)->verify(code: $data['code'], user: $user)) {
                Filament::auth()->logout();

                $this->needsMFA = false;

                $this->data['code'] = null;

                throw ValidationException::withMessages([
                    'data.email' => 'Multifactor authentication failed.',
                ]);
            }
        }

        Filament::auth()->login($user, $data['remember'] ?? false);

        session()->regenerate();

        return app(LoginResponse::class);
    }

    protected function getSsoFormActions(): array
    {
        $ssoActions = [];

        $azureSsoSettings = app(AzureSsoSettings::class);

        if ($azureSsoSettings->is_enabled && ! empty($azureSsoSettings->client_id)) {
            $ssoActions[] = Action::make('azure_sso')
                ->label(__('Login with Azure SSO'))
                ->url(route('socialite.redirect', ['provider' => 'azure']))
                ->color('gray')
                ->icon('icon-microsoft')
                ->size('sm');
        }

        $googleSsoSettings = app(GoogleSsoSettings::class);

        if ($googleSsoSettings->is_enabled && ! empty($googleSsoSettings->client_id)) {
            $ssoActions[] = Action::make('google_sso')
                ->label(__('Login with Google SSO'))
                ->url(route('socialite.redirect', ['provider' => 'google']))
                ->icon('icon-google')
                ->color('gray')
                ->size('sm');
        }

        return $ssoActions;
    }

    protected function getForms(): array
    {
        return [
            'form' => $this->form(
                $this->makeForm()
                    ->schema([
                        $this->getEmailFormComponent()
                            ->hidden(fn (Login $livewire) => $livewire->needsMFA),
                        $this->getPasswordFormComponent()
                            ->hidden(fn (Login $livewire) => $livewire->needsMFA),
                        $this->getRememberFormComponent()
                            ->hidden(fn (Login $livewire) => $livewire->needsMFA),
                        TextInput::make('code')
                            ->label('Mutlifactor Authentication Code')
                            ->placeholder('###-###')
                            ->mask('999-999')
                            ->stripCharacters('-')
                            ->numeric()
                            ->required(fn (Login $livewire) => $livewire->needsMFA)
                            ->hidden(fn (Login $livewire) => ! $livewire->needsMFA)
                            ->dehydratedWhenHidden(),
                    ])
                    ->statePath('data'),
            ),
        ];
    }
}
