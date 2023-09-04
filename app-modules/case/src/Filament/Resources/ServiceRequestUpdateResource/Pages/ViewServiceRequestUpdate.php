<?php

namespace Assist\Case\Filament\Resources\ServiceRequestUpdateResource\Pages;

use Filament\Actions;
use Illuminate\Support\Str;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ViewRecord;
use Assist\Case\Models\ServiceRequestUpdate;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Assist\Case\Enums\ServiceRequestUpdateDirection;
use Assist\Case\Filament\Resources\ServiceRequestResource;
use Assist\Case\Filament\Resources\ServiceRequestUpdateResource;

class ViewServiceRequestUpdate extends ViewRecord
{
    protected static string $resource = ServiceRequestUpdateResource::class;

    public function infolist(Infolist $infolist): Infolist
    {
        return parent::infolist($infolist)
            ->schema([
                TextEntry::make('serviceRequest.casenumber')
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
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
