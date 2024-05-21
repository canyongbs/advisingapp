<?php

namespace AdvisingApp\Ai\Filament\Resources\LegacyAiMessageLogResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;
use AdvisingApp\Ai\Filament\Exports\LegacyAiMessageExporter;
use AdvisingApp\Ai\Filament\Resources\LegacyAiMessageLogResource;

class ManageLegacyAiMessageLogs extends ManageRecords
{
    protected static string $resource = LegacyAiMessageLogResource::class;

    protected static ?string $title = 'Personal Assistant';

    protected function getHeaderActions(): array
    {
        return [
            Actions\ExportAction::make()
                ->exporter(LegacyAiMessageExporter::class),
        ];
    }
}
