<?php

namespace Assist\ServiceManagement\Filament\Resources\ServiceRequestUpdateResource\Pages;

use Filament\Actions;
use Illuminate\Support\Str;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Assist\ServiceManagement\Models\ServiceRequestUpdate;
use Assist\ServiceManagement\Enums\ServiceRequestUpdateDirection;
use Assist\ServiceManagement\Filament\Resources\ServiceRequestResource;
use Assist\ServiceManagement\Filament\Resources\ServiceRequestUpdateResource;

class ViewServiceRequestUpdate extends ViewRecord
{
    protected static string $resource = ServiceRequestUpdateResource::class;

    public function infolist(Infolist $infolist): Infolist
    {
        return parent::infolist($infolist)
            ->schema([
                Section::make()
                    ->schema([
                        TextEntry::make('serviceRequest.service_request_number')
                            ->label('Service Request')
                            ->translateLabel()
                            ->url(fn (ServiceRequestUpdate $serviceRequestUpdate): string => ServiceRequestResource::getUrl('view', ['record' => $serviceRequestUpdate]))
                            ->color('primary'),
                        IconEntry::make('internal')
                            ->boolean(),
                        TextEntry::make('direction')
                            ->icon(fn (ServiceRequestUpdateDirection $state): string => match ($state) {
                                ServiceRequestUpdateDirection::Inbound => 'heroicon-o-arrow-down-tray',
                                ServiceRequestUpdateDirection::Outbound => 'heroicon-o-arrow-up-tray',
                            })
                            ->formatStateUsing(fn (ServiceRequestUpdateDirection $state): string => Str::ucfirst($state->value)),
                        TextEntry::make('update')
                            ->columnSpanFull(),
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
