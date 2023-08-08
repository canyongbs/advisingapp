<?php

namespace Assist\Prospect\Filament\Resources\ProspectStatusResource\Pages;

use Filament\Actions;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ViewRecord;
use Assist\Prospect\Models\ProspectStatus;
use Filament\Infolists\Components\TextEntry;
use Assist\Prospect\Filament\Resources\ProspectStatusResource;

class ViewProspectStatus extends ViewRecord
{
    protected static string $resource = ProspectStatusResource::class;

    public function infolist(Infolist $infolist): Infolist
    {
        return parent::infolist($infolist)
            ->schema([
                TextEntry::make('id')
                    ->label('ID')
                    ->translateLabel(),
                TextEntry::make('name')
                    ->label('Name')
                    ->translateLabel(),
                TextEntry::make('color')
                    ->label('Color')
                    ->translateLabel()
                    ->badge()
                    ->color(fn (ProspectStatus $prospectStatus) => $prospectStatus->color),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
