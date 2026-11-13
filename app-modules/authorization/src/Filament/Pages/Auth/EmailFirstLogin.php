<?php

namespace AdvisingApp\Authorization\Filament\Pages\Auth;

use DanHarrin\LivewireRateLimiting\Exceptions\TooManyRequestsException;
use Filament\Actions\Action;
use Filament\Auth\Pages\Login;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Schema;
use Filament\Support\Enums\IconPosition;
use Filament\Support\Icons\Heroicon;
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
                ->visible(fn (): bool => $this->step === 'password'),
            $this->getPasswordFormComponent()
                ->visible(fn (): bool => $this->step === 'password'),
            $this->getRememberFormComponent()
                ->visible(fn (): bool => $this->step === 'password'),
        ]);
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
