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
                        TextEntry::make('header'),
                        TextEntry::make('footer'),
                        TextEntry::make('createdBy.name')
                            ->label('Created By')
                            ->translateLabel()
                            ->color('primary')
                            ->url(function (Division $record) {
                                return UserResource::getUrl('view', ['record' => $record->createdBy->id]);
                            }),
                        TextEntry::make('created_at')
                            ->datetime(config('project.datetime_format') ?? 'Y-m-d H:i:s'),
                        TextEntry::make('updatedBy.name')
                            ->label('Updated By')
                            ->translateLabel()
                            ->color('primary')
                            ->url(function (Division $record) {
                                return UserResource::getUrl('view', ['record' => $record->updatedBy->id]);
                            }),
                        TextEntry::make('updated_at')
                            ->datetime(config('project.datetime_format') ?? 'Y-m-d H:i:s'),
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
