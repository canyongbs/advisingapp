<?php

namespace AdvisingApp\Prospect\Filament\Resources\ProspectResource\Actions;

use AdvisingApp\StudentDataModel\Models\Student;
use Filament\Actions\Action;
use Filament\Notifications\Notification;

class DisassociateStudent extends Action {

    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->modalHeading('Remove prospect from student?')
            ->requiresConfirmation()
            ->color('danger')
            ->modalSubmitActionLabel('Yes')
            ->action(function ($record) {
                /** @var Prospect $record */
                $record->student()->dissociate();
                $record->save();
                
                Notification::make()
                    ->title('Prospect removed from student')
                    ->success()
                    ->send();

                return;
            });
    }

    public static function getDefaultName(): ?string
    {
        return 'disassociate';
    } 
}
