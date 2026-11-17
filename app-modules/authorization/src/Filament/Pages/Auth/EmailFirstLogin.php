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

namespace AdvisingApp\Authorization\Filament\Pages\Auth;

use AdvisingApp\MultifactorAuthentication\Services\MultifactorService;
use AdvisingApp\MultifactorAuthentication\Settings\MultifactorSettings;
use App\Models\User;
use DanHarrin\LivewireRateLimiting\Exceptions\TooManyRequestsException;
use Filament\Actions\Action;
use Filament\Auth\Http\Responses\Contracts\LoginResponse;
use Filament\Auth\Pages\Login;
use Filament\Facades\Filament;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Schema;
use Filament\Support\Enums\IconPosition;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\View\View;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Locked;

class EmailFirstLogin extends Login
{
    public const MOBILE_APP_USER_AGENT_TOKEN = 'AdvisingAppMobile';

    public const SWITCH_TENANT_URL = 'advisingappmobile://switch-tenant';

    protected string $view = 'authorization::email-first-login';

    /**
     * Which step of the email-first flow is shown: 'email' collects the address, 'password' the credential.
     */
    #[Locked]
    public string $step = 'email';

    #[Locked]
    public ?User $user = null;

    #[Locked]
    public bool $needsMfaSetup = false;

    #[Locked]
    public bool $needsMfa = false;

    #[Locked]
    public bool $usingRecoveryCode = false;

    #[Locked]
    public bool $hasVerifiedMfaSetup = false;

    public function form(Schema $schema): Schema
    {
        // Two distinct email inputs (different keys) so that on the password step Livewire renders a
        // fresh, disabled input rather than morphing the editable one the user typed into (which it
        // keeps editable). The disabled input stays dehydrated so its value is still submitted.
        return $schema->components([
            $this->getEmailFormComponent()
                ->visible(fn (): bool => $this->step === 'email'),
            $this->getEmailFormComponent()
                ->key('emailConfirmed')
                ->disabled()
                ->dehydrated()
                ->dehydratedWhenHidden(fn (): bool => $this->needsMfa)
                ->visible(fn (): bool => $this->step === 'password' && ! $this->needsMfa),
            $this->getPasswordFormComponent()
                ->dehydratedWhenHidden(fn (): bool => $this->needsMfa)
                ->visible(fn (): bool => $this->step === 'password' && ! $this->needsMfa),
            $this->getRememberFormComponent()
                ->dehydratedWhenHidden(fn (): bool => $this->needsMfa)
                ->visible(fn (): bool => $this->step === 'password' && ! $this->needsMfa),
            TextInput::make('code')
                ->label(
                    fn (): string => $this->usingRecoveryCode
                        ? 'Multifactor Recovery Code'
                        : 'Multifactor Authentication Code'
                )
                ->placeholder(fn (): string => $this->usingRecoveryCode ? 'abcdef-98765' : '###-###')
                ->mask(fn (): ?string => $this->usingRecoveryCode ? null : '999-999')
                ->stripCharacters(fn (): ?string => $this->usingRecoveryCode ? null : '-')
                ->helperText(
                    fn (): ?string => $this->usingRecoveryCode
                        ? 'Enter one of your recovery codes provided when you enabled multifactor authentication. Recovery codes are one-time use only. If you have used all of your recovery codes, you will need to contact your administrator to reset your multifactor authentication.'
                        : null
                )
                ->rule('numeric', fn (): bool => ! $this->usingRecoveryCode)
                ->string(fn (): bool => $this->usingRecoveryCode)
                ->required(fn (): bool => $this->needsMfa)
                ->visible(fn (): bool => $this->needsMfa),
        ]);
    }

    public function authenticate(): ?LoginResponse
    {
        $this->hasVerifiedMfaSetup = false;

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

        $user = Filament::auth()->user();
        assert($user instanceof User);

        $this->user = $user;

        if (! $user->canAccessPanel(Filament::getCurrentOrDefaultPanel())) {
            Filament::auth()->logout();

            $this->throwFailureValidationException();
        }

        $mfaSettings = app(MultifactorSettings::class);

        if ($mfaSettings->required && ! $user->hasConfirmedMultifactor()) {
            if (! $user->hasEnabledMultifactor() || empty($data['code'])) {
                $user->enableMultifactorAuthentication();

                Filament::auth()->logout();

                $this->needsMfaSetup = true;
                $this->needsMfa = true;

                return null;
            }
        }

        if ($user->hasEnabledMultifactor()) {
            if (empty($data['code'])) {
                Filament::auth()->logout();

                $this->needsMfa = true;

                return null;
            }

            if (! $this->isValidCode($user, $data['code'])) {
                Filament::auth()->logout();

                $this->data['code'] = null;

                throw ValidationException::withMessages([
                    'data.code' => 'Multifactor authentication failed.',
                ]);
            }

            if (empty($user->multifactor_confirmed_at)) {
                $user->confirmMultifactorAuthentication();

                Filament::auth()->logout();

                $this->hasVerifiedMfaSetup = true;

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
            ->visible(fn (): bool => $this->hasVerifiedMfaSetup && $this->user !== null)
            ->requiresConfirmation()
            ->modalDescription('')
            ->modalCancelAction(false)
            ->closeModalByClickingAway(false)
            ->closeModalByEscaping(false)
            ->modalCloseButton(false)
            ->modalSubmitActionLabel('Okay')
            ->modalContent(function (): View {
                $codes = $this->user->multifactor_recovery_codes ?? [];

                assert(is_array($codes));

                return view('multifactor-authentication::filament.actions.recovery-codes-modal', [
                    'codes' => collect($codes),
                ]);
            })
            ->action(function (array $arguments): void {
                assert($this->user instanceof User);

                Filament::auth()->login($this->user, $arguments['remember'] ?? false);

                session()->regenerate();

                redirect()->intended(Filament::getUrl());
            });
    }

    public function toggleUsingRecoveryCodes(): void
    {
        $this->usingRecoveryCode = ! $this->usingRecoveryCode;
    }

    /**
     * Resolve the entered email and route the user on to the password step. Unknown emails also
     * advance to the password step so we never reveal whether an account exists.
     */
    public function continue(): void
    {
        try {
            $this->rateLimit(5);
        } catch (TooManyRequestsException $exception) {
            $this->getRateLimitedNotification($exception)?->send();

            return;
        }

        $this->form->getState();

        $this->step = 'password';

        // Let the view move focus to the freshly rendered password input so the user can start typing.
        $this->dispatch('login-password-step');
    }

    public function backToEmailStep(): void
    {
        $this->step = 'email';
        $this->data['password'] = null;
    }

    public function isMobileApp(): bool
    {
        return str((string) request()->userAgent())->contains(self::MOBILE_APP_USER_AGENT_TOKEN);
    }

    protected function getMultifactorQrCode(): string
    {
        return app(MultifactorService::class)->getMultifactorQrCodeSvg($this->user->getMultifactorQrCodeUrl());
    }

    protected function throwFailureValidationException(): never
    {
        // The email input is disabled on the password step, so surface the generic failure on the password.
        throw ValidationException::withMessages([
            'data.password' => __('filament-panels::auth/pages/login.messages.failed'),
        ]);
    }

    protected function getEmailFormComponent(): TextInput
    {
        $component = parent::getEmailFormComponent();

        assert($component instanceof TextInput);

        return $component
            ->label('Email address')
            ->placeholder('you@yourorganization.com')
            ->prefixIcon(Heroicon::Envelope);
    }

    protected function getAuthenticateFormAction(): Action
    {
        return parent::getAuthenticateFormAction()
            ->label('Log in');
    }

    protected function getRememberFormComponent(): Component
    {
        return parent::getRememberFormComponent()->default(true);
    }

    protected function isValidCode(User $user, string $code): bool
    {
        if ($this->usingRecoveryCode) {
            $recoveryCodes = $user->multifactor_recovery_codes ?? [];

            assert(is_array($recoveryCodes));

            return collect($recoveryCodes)->contains(function (string $recoveryCode) use ($code): bool {
                return hash_equals($recoveryCode, $code);
            });
        }

        return app(MultifactorService::class)->verify(code: $code, user: $user);
    }

    /**
     * @return array<Action>
     */
    protected function getFormActions(): array
    {
        if ($this->step === 'email') {
            return [
                $this->getContinueFormAction(),
            ];
        }

        return [
            $this->getAuthenticateFormAction(),
        ];
    }

    protected function getContinueFormAction(): Action
    {
        return Action::make('continue')
            ->label('Continue')
            ->icon(Heroicon::ArrowRight)
            ->iconPosition(IconPosition::After)
            ->submit('continue');
    }
}
