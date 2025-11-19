<?php

namespace AdvisingApp\GroupAppointment\Filament\Resources\BookingGroups\Pages;

use AdvisingApp\GroupAppointment\Filament\Resources\BookingGroups\BookingGroupResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListBookingGroups extends ListRecords
{
    protected static string $resource = BookingGroupResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
