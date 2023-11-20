<?php

namespace Assist\Task\Filament\Resources\TaskResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Assist\Task\Filament\Resources\TaskResource;

class ViewTask extends ViewRecord
{
    protected static string $resource = TaskResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
