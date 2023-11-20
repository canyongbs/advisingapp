<?php

namespace Assist\Engagement\Filament\Resources\EngagementFileResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Assist\Engagement\Filament\Resources\EngagementFileResource;

class EditEngagementFile extends EditRecord
{
    protected static string $resource = EngagementFileResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
