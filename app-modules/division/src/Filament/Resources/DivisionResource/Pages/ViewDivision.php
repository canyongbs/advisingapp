<?php

namespace Assist\Division\Filament\Resources\DivisionResource\Pages;

use Filament\Actions\EditAction;
use Filament\Infolists\Infolist;
use Assist\Division\Models\Division;
use App\Filament\Resources\UserResource;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Assist\Division\Filament\Resources\DivisionResource;

class ViewDivision extends ViewRecord
{
    protected static string $resource = DivisionResource::class;

    public function infolist(Infolist $infolist): Infolist
    {
        return parent::infolist($infolist)
            ->schema([
                Section::make()
                    ->schema([
                        TextEntry::make('id')
                            ->label('ID'),
                        TextEntry::make('name'),
                        TextEntry::make('code'),
                        Section::make()
                            ->schema([
                                TextEntry::make('createdBy.name')
                                    ->default('N/A')
                                    ->label('Created By')
                                    ->color(fn (Division $record) => $record->createdBy ? 'primary' : null)
                                    ->url(fn (Division $record) => $record->createdBy ? UserResource::getUrl('view', ['record' => $record->createdBy]) : null),
                                TextEntry::make('created_at')
                                    ->datetime(config('project.datetime_format') ?? 'Y-m-d H:i:s'),
                                TextEntry::make('lastUpdatedBy.name')
                                    ->default('N/A')
                                    ->label('Last Updated By')
                                    ->color(fn (Division $record) => $record->lastUpdatedBy ? 'primary' : null)
                                    ->url(fn (Division $record) => $record->lastUpdatedBy ? UserResource::getUrl('view', ['record' => $record->lastUpdatedBy]) : null),
                                TextEntry::make('updated_at')
                                    ->datetime(config('project.datetime_format') ?? 'Y-m-d H:i:s'),
                            ])
                            ->columns(),
                        TextEntry::make('header')
                            ->columnSpanFull()
                            ->view('filament.infolists.entries.html'),
                        TextEntry::make('footer')
                            ->columnSpanFull()
                            ->view('filament.infolists.entries.html'),
                    ])
                    ->columns(),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
