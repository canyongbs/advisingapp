<?php

namespace Assist\ServiceManagement\Filament\Resources\ServiceRequestStatusResource\Pages;

use Filament\Actions\EditAction;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Assist\ServiceManagement\Models\ServiceRequestStatus;
use Assist\ServiceManagement\Filament\Resources\ServiceRequestStatusResource;

class ViewServiceRequestStatus extends ViewRecord
{
    protected static string $resource = ServiceRequestStatusResource::class;

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make()
                    ->schema([
                        TextEntry::make('name')
                            ->label('Name')
                            ->translateLabel(),
                        TextEntry::make('classification')
                            ->label('Classification')
                            ->translateLabel(),
                        TextEntry::make('color')
                            ->label('Color')
                            ->translateLabel()
                            ->badge()
                            ->color(fn (ServiceRequestStatus $serviceRequestStatus) => $serviceRequestStatus->color),
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
