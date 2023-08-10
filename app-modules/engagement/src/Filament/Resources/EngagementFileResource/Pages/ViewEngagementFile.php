<?php

namespace Assist\Engagement\Filament\Resources\EngagementFileResource\Pages;

use Assist\Engagement\Filament\Resources\EngagementFileResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewEngagementFile extends ViewRecord
{
    protected static string $resource = EngagementFileResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
