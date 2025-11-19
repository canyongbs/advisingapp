<?php

namespace AdvisingApp\GroupAppointment\Filament\Resources\BookingGroups\Pages;

use AdvisingApp\GroupAppointment\Filament\Resources\BookingGroups\BookingGroupResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewBookingGroup extends ViewRecord
{
    protected static string $resource = BookingGroupResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
