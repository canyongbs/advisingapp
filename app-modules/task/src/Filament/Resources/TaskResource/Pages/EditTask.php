<?php

namespace Assist\Task\Filament\Resources\TaskResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Assist\Task\Filament\Resources\TaskResource;

class EditTask extends EditRecord
{
    protected static string $resource = TaskResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
