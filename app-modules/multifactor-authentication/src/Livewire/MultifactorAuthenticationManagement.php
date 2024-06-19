<?php

namespace AdvisingApp\MultifactorAuthentication\Livewire;

use App\Models\User;
use Livewire\Component;
use Filament\Actions\Action;
use Filament\Facades\Filament;
use Illuminate\Support\Collection;
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

    public User $user;

    public int $code;

    public bool $showRecoveryCodes = false;

    public function render()
    {
        return view('multifactor-authentication::livewire.multifactor-authentication-management');
    }

    public function mount()
    {
        $this->user = $this->user ?? Filament::getCurrentPanel()->auth()->user();
    }

    public function enableAction(): Action
    {
        return PasswordButtonAction::make('enable')
            ->label('Enable')
            ->action(function () {
                $this->user->enableMultifactorAuthentication();

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
                $this->user->disableMultifactorAuthentication();

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

                $this->user->confirmMultifactorAuthentication();

                Notification::make()
                    ->success()
                    ->title('Code verified. Two factor authentication enabled.')
                    ->send();

                $this->replaceMountedAction('recoveryCodes');
            });
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
                'codes' => $this->recoveryCodes,
            ]));
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

                $this->replaceMountedAction('recoveryCodes');
            });
    }

    public function getRecoveryCodesProperty(): Collection
    {
        return collect($this->user->multifactor_recovery_codes ?? []);
    }

    public function getMultifactorQrCode()
    {
        return app(MultifactorService::class)->getMultifactorQrCodeSvg($this->user->getMultifactorQrCodeUrl());
    }

    public function toggleRecoveryCodes()
    {
        $this->showRecoveryCodes = ! $this->showRecoveryCodes;
    }
}
