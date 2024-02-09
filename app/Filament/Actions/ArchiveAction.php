<?php

namespace App\Filament\Actions;

use Filament\Actions\Action;
use App\Models\Contracts\Archivable;
use Filament\Notifications\Notification;

class ArchiveAction extends Action
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->icon('heroicon-o-archive-box')
            ->visible(fn (Archivable $record) => $record->isArchivable())
            ->requiresConfirmation()
            ->modalDescription('Are you sure you want to archive this record?')
            ->action(function (Archivable $record) {
                $record->archive();

                Notification::make()
                    ->title('Record Archived')
                    ->success()
                    ->send();
            });
    }

    public static function getDefaultName(): ?string
    {
        return 'archive';
    }
}
