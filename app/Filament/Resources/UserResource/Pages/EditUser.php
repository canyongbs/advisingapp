<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Models\User;
use Filament\Actions;
use App\Filament\Resources\UserResource;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use App\Notifications\SetPasswordNotification;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        /** @var User $user */
        $user = $this->getRecord();

        return [
            Actions\Action::make('resetPassword')
                ->color('gray')
                ->requiresConfirmation()
                ->modalDescription('This will remove the user\'s current password and send them an email with a link to set a new password.')
                ->hidden($user->is_external)
                ->action(function () use ($user) {
                    $user->password = null;
                    $user->save();

                    $user->notify(new SetPasswordNotification());

                    Notification::make()
                        ->title('The password has been reset')
                        ->success()
                        ->send();
                }),
            Actions\DeleteAction::make(),
        ];
    }
}
