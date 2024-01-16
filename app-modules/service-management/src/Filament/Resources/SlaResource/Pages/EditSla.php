<?php

namespace AdvisingApp\ServiceManagement\Filament\Resources\SlaResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use AdvisingApp\ServiceManagement\Filament\Resources\SlaResource;

class EditSla extends EditRecord
{
    protected static string $resource = SlaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
