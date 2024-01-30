<?php

namespace AdvisingApp\Report\Filament\Exports;

use App\Models\User;
use Filament\Actions\Exports\Exporter;
use Filament\Tables\Columns\TextColumn;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Models\Export;

class UserExporter extends Exporter
{
    protected static ?string $model = User::class;

    /**
     * @param class-string<TextColumn | ExportColumn> $type
     */
    public static function getColumns(string $type = ExportColumn::class): array
    {
        return [
            $type::make('id')
                ->label('ID'),
            $type::make('name'),
            $type::make('email'),
            $type::make('phone_number'),
            $type::make('created_at'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your user report export has completed and ' . number_format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
        }

        return $body;
    }
}
