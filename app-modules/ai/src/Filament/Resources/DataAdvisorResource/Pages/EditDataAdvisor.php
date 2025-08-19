<?php

namespace AdvisingApp\Ai\Filament\Resources\DataAdvisorResource\Pages;

use AdvisingApp\Ai\Filament\Resources\DataAdvisorResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditDataAdvisor extends EditRecord
{
    protected static string $resource = DataAdvisorResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
