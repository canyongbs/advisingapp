<?php

namespace AdvisingApp\Assistant\Filament\Exports;

use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Models\Export;
use Filament\Actions\Exports\Enums\ExportFormat;
use AdvisingApp\Assistant\Models\AssistantChatMessageLog;

class AssistantChatMessageLogExporter extends Exporter
{
    protected static ?string $model = AssistantChatMessageLog::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('message'),
            ExportColumn::make('metadata')
                ->listAsJson(),
            ExportColumn::make('user.name'),
            ExportColumn::make('request')
                ->listAsJson(),
            ExportColumn::make('sent_at'),
            ExportColumn::make('created_at'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your assistant chat message log export has completed and ' . number_format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
        }

        return $body;
    }

    /**
     * Using CSV format causes issues with the JSON commas not being properly escaped by league/csv.
     */
    public function getFormats(): array
    {
        return [ExportFormat::Xlsx];
    }
}
