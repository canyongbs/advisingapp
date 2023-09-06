<?php

namespace Assist\ServiceManagement\Filament\Resources\ServiceRequestTypeResource\Pages;

use Filament\Actions\EditAction;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists\Components\TextEntry;
use Assist\ServiceManagement\Filament\Resources\ServiceRequestTypeResource;

class ViewServiceRequestType extends ViewRecord
{
    protected static string $resource = ServiceRequestTypeResource::class;

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
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
