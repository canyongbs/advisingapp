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

/**
 * @property-read Collection $recoveryCodes
 */
class MultifactorAuthenticationManagement extends Component implements HasActions, HasForms
{
    use InteractsWithActions;
    use InteractsWithForms;

    public User $user;

    public int $code;

    public function render()
    {
        return view('multifactor-authentication::livewire.multifactor-authentication-management');
    }

    public function mount()
    {
        $this->user ??= Filament::getCurrentPanel()->auth()->user();
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
                    ->title('Code verified. Multifactor authentication enabled.')
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
}
