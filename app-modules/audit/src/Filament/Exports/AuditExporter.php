<?php

namespace AdvisingApp\Audit\Filament\Exports;

use AdvisingApp\Audit\Models\Audit;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Models\Export;
use Filament\Actions\Exports\Enums\ExportFormat;

class AuditExporter extends Exporter
{
    protected static ?string $model = Audit::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('event'),
            ExportColumn::make('auditable_type'),
            ExportColumn::make('auditable_id')
                ->label('Auditable ID'),
            ExportColumn::make('old_values')
                ->listAsJson(),
            ExportColumn::make('new_values')
                ->listAsJson(),
            ExportColumn::make('url'),
            ExportColumn::make('ip_address')
                ->label('IP address'),
            ExportColumn::make('user_agent'),
            ExportColumn::make('tags'),
            ExportColumn::make('created_at'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your audit export has completed and ' . number_format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

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
