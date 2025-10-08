<?php

namespace AdvisingApp\Report\Filament\Exports;

use AdvisingApp\Research\Models\ResearchRequest;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class ResearchAdvisorReportTableExporter extends Exporter
{
    protected static ?string $model = ResearchRequest::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('user.name')
                ->label('Created By'),
            ExportColumn::make('title')
                ->label('Name'),
            ExportColumn::make('created_at')
                ->label('Created At'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your research advisor report table export has completed and ' . number_format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
        }

        return $body;
    }
}
