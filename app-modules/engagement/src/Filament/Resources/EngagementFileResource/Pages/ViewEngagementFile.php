<?php

namespace Assist\Engagement\Filament\Resources\EngagementFileResource\Pages;

use Filament\Actions;
use Illuminate\Support\Carbon;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ViewRecord;
use Assist\Engagement\Models\EngagementFile;
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
                TextEntry::make('id')
                    ->label('ID'),
                TextEntry::make('description')
                    ->label('Description'),
                SpatieMediaLibraryImageEntry::make('file')
                    ->collection('file')
                    ->hintAction(
                        Action::make('download')
                            ->label('Download')
                            ->icon('heroicon-m-arrow-down-tray')
                            ->color('primary')
                            ->extraAttributes([
                                'download' => true,
                                'target' => '_blank',
                            ])
                            ->url(fn (EngagementFile $record) => $record->getFirstMedia('file')?->getTemporaryUrl(Carbon::now()->addMinutes(5)))
                    ),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
