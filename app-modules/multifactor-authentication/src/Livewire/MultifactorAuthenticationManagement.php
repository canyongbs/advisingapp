<?php

namespace AdvisingApp\MultifactorAuthentication\Livewire;

use Livewire\Component;
use Filament\Actions\Action;
use Filament\Facades\Filament;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Actions\Concerns\InteractsWithActions;
use AdvisingApp\MultifactorAuthentication\Services\MultifactorService;
use AdvisingApp\MultifactorAuthentication\Filament\Actions\PasswordButtonAction;

class MultifactorAuthenticationManagement extends Component implements HasActions, HasForms
{
    use InteractsWithActions;
    use InteractsWithForms;

    public $user;

    public $code;

    public bool $showRecoveryCodes = false;

    public function render()
    {
        return view('multifactor-authentication::livewire.multifactor-authentication-management');
    }

    public function mount()
    {
        $this->user = Filament::getCurrentPanel()->auth()->user();
    }

    public function enableAction(): Action
    {
        return PasswordButtonAction::make('enable')
            ->label('Enable')
            ->action(function () {
                Log::debug('hello');
                $this->user->enableTwoFactorAuthentication();

                Notification::make()
                    ->success()
                    ->title('Two factor authentication enabled.')
                    ->send();
            });
    }

    public function disableAction(): Action
    {
        return PasswordButtonAction::make('disable')
            ->label('Disable')
            ->color('primary')
            ->requiresConfirmation()
            ->action(function () {
                $this->user->disableTwoFactorAuthentication();

                Notification::make()
                    ->warning()
                    ->title('Two factor authentication has been disabled.')
                    ->send();
            });
    }

    public function confirmAction(): Action
    {
        return Action::make('confirm')
            ->color('success')
            ->label('Confirm & finish')
            ->modalWidth('sm')
            ->form([
                TextInput::make('code')
                    ->label('Code')
                    ->placeholder('###-###')
                    ->mask('999-999')
                    ->stripCharacters('-')
                    ->numeric()
                    ->required(),
            ])
            ->action(function ($data, $action, $livewire) {
                if (! app(MultifactorService::class)->verify(code: $data['code'])) {
                    $livewire->addError('mountedActionsData.0.code', 'The code you have entered is invalid.');
                    $action->halt();
                }

                $this->user->confirmTwoFactorAuthentication();

                Notification::make()
                    ->success()
                    ->title('Code verified. Two factor authentication enabled.')
                    ->send();
            });
    }

    public function regenerateCodesAction(): Action
    {
        return PasswordButtonAction::make('regenerateCodes')
            ->label('Regenerate Recovery Codes')
            ->requiresConfirmation()
            ->action(function () {
                $this->user->reGenerateRecoveryCodes();

                $this->showRecoveryCodes = true;

                Notification::make()
                    ->success()
                    ->title('New recovery codes have been generated.')
                    ->send();
            });
    }

    public function getRecoveryCodesProperty(): Collection
    {
        return collect($this->user->two_factor_recovery_codes ?? []);
    }

    public function getTwoFactorQrCode()
    {
        return app(MultifactorService::class)->getTwoFactorQrCodeSvg($this->user->getTwoFactorQrCodeUrl());
    }

    public function toggleRecoveryCodes()
    {
        $this->showRecoveryCodes = ! $this->showRecoveryCodes;
    }

    public function showRequiresTwoFactorAlert()
    {
        return app(MultifactorService::class)->shouldForceTwoFactor();
    }
}
