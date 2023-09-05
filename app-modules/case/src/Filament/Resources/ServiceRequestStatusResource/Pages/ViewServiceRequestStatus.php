<?php

namespace Assist\Case\Filament\Resources\ServiceRequestStatusResource\Pages;

use Filament\Actions\EditAction;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ViewRecord;
use Assist\Case\Models\ServiceRequestStatus;
use Filament\Infolists\Components\TextEntry;
use Assist\Case\Filament\Resources\ServiceRequestStatusResource;

class ViewServiceRequestStatus extends ViewRecord
{
    protected static string $resource = ServiceRequestStatusResource::class;

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
                    ->color(fn (ServiceRequestStatus $serviceRequestStatus) => $serviceRequestStatus->color),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
