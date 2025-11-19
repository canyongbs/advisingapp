<?php

namespace AdvisingApp\GroupAppointment\Filament\Resources\BookingGroups\Pages;

use AdvisingApp\GroupAppointment\Filament\Resources\BookingGroups\BookingGroupResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditBookingGroup extends EditRecord
{
    protected static string $resource = BookingGroupResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
