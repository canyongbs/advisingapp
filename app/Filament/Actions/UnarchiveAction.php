<?php

namespace App\Filament\Actions;

use Filament\Actions\Action;
use App\Models\Contracts\Archivable;
use Filament\Notifications\Notification;

class UnarchiveAction extends Action
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->icon('heroicon-o-archive-box-x-mark')
            ->visible(fn (Archivable $record) => $record->isUnarchivable())
            ->requiresConfirmation()
            ->modalDescription('Are you sure you want to unarchive this record?')
            ->action(function (Archivable $record) {
                $record->unarchive();

                Notification::make()
                    ->title('Record Unarchived')
                    ->success()
                    ->send();
            });
    }

    public static function getDefaultName(): ?string
    {
        return 'unarchive';
    }
}
