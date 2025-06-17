<?php

namespace AdvisingApp\Ai\Filament\Resources\QnAAdvisorResource\Pages;

use AdvisingApp\Ai\Filament\Resources\AiAssistantResource;
use AdvisingApp\Ai\Filament\Resources\QnAAdvisorResource;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\SpatieMediaLibraryImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;

class ViewQnAAdvisor extends ViewRecord
{
    protected static string $resource = QnAAdvisorResource::class;

    protected static ?string $navigationLabel = 'View';

    public function infolist(Infolist $infolist):Infolist
    {
        return $infolist->schema([
           Section::make()->schema([
                SpatieMediaLibraryImageEntry::make('avatar')->visibility('private')->collection('avatar')->circular(),
                TextEntry::make('name'),
                TextEntry::make('description'),
                TextEntry::make('model'),
            ]),
        ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('archive')
                ->color('danger')
                ->action(function () {
                    $assistant = $this->getRecord();
                    $assistant->archived_at = now();
                    $assistant->save();

                    Notification::make()
                        ->title('QnA Advisor archived')
                        ->success()
                        ->send();
                })
                ->hidden(fn (): bool => (bool) $this->getRecord()->archived_at),
            Action::make('restore')
                ->action(function () {
                    $assistant = $this->getRecord();
                    $assistant->archived_at = null;
                    $assistant->save();

                    Notification::make()
                        ->title('QnA Advisor restored')
                        ->success()
                        ->send();
                })
                ->hidden(function (): bool {
                    if (! $this->getRecord()->archived_at) {
                        return true;
                    }

                    return false;
                }),
        ];
    }
}
