<?php

namespace Assist\Engagement\Filament\Resources\EngagementFileResource\Pages;

use Filament\Actions;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\Actions\Action;
use Filament\Infolists\Components\SpatieMediaLibraryImageEntry;
use Assist\Engagement\Filament\Resources\EngagementFileResource;

class ViewEngagementFile extends ViewRecord
{
    protected static string $resource = EngagementFileResource::class;

    public function infolist(Infolist $infolist): Infolist
    {
        return parent::infolist($infolist)
            ->schema([
                Section::make()
                    ->schema([
                        TextEntry::make('description')
                            ->label('Description'),
                        SpatieMediaLibraryImageEntry::make('file')
                            ->collection('file')
                            ->visibility('private')
                            ->hintAction(
                                Action::make('download')
                                    ->label('Download')
                                    ->icon('heroicon-m-arrow-down-tray')
                                    ->color('primary')
                                    ->extraAttributes([
                                        'download' => true,
                                        'target' => '_blank',
                                    ])
                                    ->url(route('engagement-file-download', ['file' => $this->record->id]))
                            ),
                    ])
                    ->columns(),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
