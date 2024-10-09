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
use Livewire\Attributes\Locked;
use Filament\Forms\Components\TextInput;
use Filament\Models\Contracts\FilamentUser;
use AdvisingApp\Theme\Settings\ThemeSettings;
use Illuminate\Validation\ValidationException;
use Filament\Pages\Auth\Login as FilamentLogin;
use AdvisingApp\Authorization\Settings\AzureSsoSettings;
use AdvisingApp\Authorization\Settings\GoogleSsoSettings;
use Filament\Http\Responses\Auth\Contracts\LoginResponse;
use AdvisingApp\MultifactorAuthentication\Services\MultifactorService;
use AdvisingApp\MultifactorAuthentication\Settings\MultifactorSettings;
use DanHarrin\LivewireRateLimiting\Exceptions\TooManyRequestsException;

class Login extends FilamentLogin
{
    protected static string $view = 'authorization::login';

    protected static string $layout = 'filament-panels::components.layout.login';

    public ?array $data;

    #[Locked]
    public ?User $user;

    #[Locked]
    public bool $needsMfaSetup = false;

    #[Locked]
    public bool $needsMFA = false;

    #[Locked]
    public bool $usingRecoveryCode = false;

    public string $themeChangelogUrl = '';

    public string $productKnowledgebaseUrl = '';

    public function mount(): void
    {
        $themeSettings = app(ThemeSettings::class);

        $this->themeChangelogUrl = ! empty($themeSettings->changelog_url) ? $themeSettings->changelog_url : 'https://advising.app/changelog/';

        $this->productKnowledgebaseUrl = ! empty($themeSettings->product_knowledge_base_url) ? $themeSettings->product_knowledge_base_url : 'https://canyongbs.aiding.app/portal/categories/9bcc47d1-05be-40d2-bf95-9bd719209b06';
    }

    public function authenticate(): ?LoginResponse
    {
        $this->user = null;

        try {
            $this->rateLimit(5);
        } catch (TooManyRequestsException $exception) {
            $this->getRateLimitedNotification($exception)?->send();

            return null;
        }

        $data = $this->form->getState();

        if (! Filament::auth()->once($this->getCredentialsFromFormData($data))) {
            $this->throwFailureValidationException();
        }

        /** @var User $user */
        $user = Filament::auth()->user();

        $this->user = $user;

        if (
            ($user instanceof FilamentUser) &&
            (! $user->canAccessPanel(Filament::getCurrentPanel()))
        ) {
            Filament::auth()->logout();

            $this->throwFailureValidationException();
        }

        $mfaSettings = app(MultifactorSettings::class);

        if ($mfaSettings->required) {
            if (! $user->hasConfirmedMultifactor() && empty($data['code'])) {
                $user->enableMultifactorAuthentication();

                $this->needsMfaSetup = true;
                $this->needsMFA = true;

                return null;
            }
        }

        if ($user->hasEnabledMultifactor()) {
            if (empty($data['code'])) {
                Filament::auth()->logout();

                $this->needsMFA = true;

                return null;
            }

            if (! $this->isValidCode($user, $data['code'])) {
                Filament::auth()->logout();

                $this->needsMFA = false;

                $this->usingRecoveryCode = false;

                $this->data['code'] = null;

                throw ValidationException::withMessages([
                    'data.email' => 'Multifactor authentication failed.',
                ]);
            }

            if (empty($user->multifactor_confirmed_at)) {
                $user->confirmMultifactorAuthentication();

                $this->mountAction('recoveryCodes', [
                    'user' => $user,
                    'remember' => $data['remember'],
                ]);

                return null;
            }

            if ($this->usingRecoveryCode) {
                $user->destroyRecoveryCode($data['code']);
            }
        }

        Filament::auth()->login($user, $data['remember'] ?? false);

        session()->regenerate();

        return app(LoginResponse::class);
    }

    public function recoveryCodesAction(): Action
    {
        return Action::make('recoveryCodes')
            ->label('Recovery Codes')
            ->requiresConfirmation()
            ->modalDescription('')
            ->modalCancelAction(false)
            ->closeModalByClickingAway(false)
            ->closeModalByEscaping(false)
            ->modalCloseButton(false)
            ->modalSubmitActionLabel('Okay')
            ->modalContent(view('multifactor-authentication::filament.actions.recovery-codes-modal', [
                'codes' => collect($this->user->multifactor_recovery_codes),
            ]))
            ->action(function (array $arguments) {
                Filament::auth()->login($arguments['user'], $arguments['remember'] ?? false);

                session()->regenerate();

                redirect()->route('filament.admin.pages.dashboard');
            });
    }

    public function getMultifactorQrCode()
    {
        return app(MultifactorService::class)->getMultifactorQrCodeSvg($this->user->getMultifactorQrCodeUrl());
    }

    public function toggleUsingRecoveryCodes(): void
    {
        $this->usingRecoveryCode = ! $this->usingRecoveryCode;
    }

    protected function isValidCode(User $user, string $code): bool
    {
        if ($this->usingRecoveryCode) {
            return collect($user->multifactor_recovery_codes)->contains(function (string $recoveryCode) use ($code) {
                return hash_equals($recoveryCode, $code);
            });
        }

        return app(MultifactorService::class)->verify(code: $code, user: $user);
    }

    protected function getSsoFormActions(): array
    {
        $ssoActions = [];

        $azureSsoSettings = app(AzureSsoSettings::class);

        if ($azureSsoSettings->is_enabled && ! empty($azureSsoSettings->client_id)) {
            $ssoActions[] = Action::make('azure_sso')
                ->label(__('Microsoft'))
                ->url(route('socialite.redirect', ['provider' => 'azure']))
                ->color('gray')
                ->icon('icon-microsoft')
                ->size('sm')
                ->extraAttributes(['class' => 'dark_button_border']);
        }

        $googleSsoSettings = app(GoogleSsoSettings::class);

        if ($googleSsoSettings->is_enabled && ! empty($googleSsoSettings->client_id)) {
            $ssoActions[] = Action::make('google_sso')
                ->label(__('Google'))
                ->url(route('socialite.redirect', ['provider' => 'google']))
                ->icon('icon-google')
                ->color('gray')
                ->size('sm')
                ->extraAttributes(['class' => 'dark_button_border']);
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
                            ->label('Email')
                            ->hidden(fn (Login $livewire) => $livewire->needsMFA)
                            ->dehydratedWhenHidden(),
                        $this->getPasswordFormComponent()
                            ->hidden(fn (Login $livewire) => $livewire->needsMFA)
                            ->dehydratedWhenHidden(),
                        $this->getRememberFormComponent()
                            ->hidden(fn (Login $livewire) => $livewire->needsMFA)
                            ->dehydratedWhenHidden(),
                        TextInput::make('code')
                            ->label(
                                fn (Login $livewire) => ! $livewire->usingRecoveryCode
                                    ? 'Multifactor Authentication Code'
                                    : 'Multifactor Recovery Code'
                            )
                            ->placeholder(
                                fn (Login $livewire) => ! $livewire->usingRecoveryCode
                                    ? '###-###'
                                    : 'abcdef-98765'
                            )
                            ->mask(
                                fn (Login $livewire) => ! $livewire->usingRecoveryCode
                                    ? '999-999'
                                    : null
                            )
                            ->stripCharacters(
                                fn (Login $livewire) => ! $livewire->usingRecoveryCode
                                    ? '-'
                                    : null
                            )
                            ->helperText(
                                fn (Login $livewire) => $livewire->usingRecoveryCode
                                    ? 'Enter one of your recovery codes provided when you enabled multifactor authentication. Recovery codes are one-time use only. If you have used all of your recovery codes, you will need to contact your administrator to reset your multifactor authentication.'
                                    : null
                            )
                            ->numeric(fn (Login $livewire) => ! $livewire->usingRecoveryCode)
                            ->string(fn (Login $livewire) => $livewire->usingRecoveryCode)
                            ->required(fn (Login $livewire) => $livewire->needsMFA)
                            ->hidden(fn (Login $livewire) => ! $livewire->needsMFA)
                            ->dehydratedWhenHidden(),
                    ])
                    ->statePath('data'),
            ),
        ];
    }

    protected function getFormActions(): array
    {
        return [
            parent::getAuthenticateFormAction()
                ->label('Log in')
                ->extraAttributes(['class' => 'dark:bg-gray-800 dark_button_border dark:hover:bg-gray-700']),
        ];
    }
}
