<?php

namespace AdvisingApp\Report\Filament\Exports;

use AdvisingApp\Prospect\Models\Prospect;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class ProspectReportTableExportExporter extends Exporter
{
    protected static ?string $model = Prospect::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('full_name')
                ->label('Full Name'),
            ExportColumn::make('primaryEmailAddress.address')
                ->label('Email'),
            ExportColumn::make('status.name'),
            ExportColumn::make('createdBy.name')
                ->label('Created By'),
            ExportColumn::make('created_at'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your most recent prospects report table export has completed and ' . number_format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
        }

        return $body;
    }
}
