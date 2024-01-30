<?php

namespace AdvisingApp\Report\Filament\Exports;

use Filament\Actions\Exports\Exporter;
use Filament\Tables\Columns\TextColumn;
use AdvisingApp\Prospect\Models\Prospect;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Models\Export;

class ProspectExporter extends Exporter
{
    protected static ?string $model = Prospect::class;

    /**
     * @param class-string<TextColumn | ExportColumn> $type
     */
    public static function getColumns(string $type = ExportColumn::class): array
    {
        return [
            $type::make('id')
                ->label('ID'),
            $type::make('status.name'),
            $type::make('source.name'),
            $type::make('first_name'),
            $type::make('last_name'),
            $type::make('full_name'),
            $type::make('preferred')
                ->label('Preferred Name'),
            $type::make('description'),
            $type::make('email'),
            $type::make('email_2')
                ->label('Email 2'),
            $type::make('mobile'),
            $type::make('phone'),
            $type::make('address'),
            $type::make('address_2')
                ->label('Address 2'),
            $type::make('birthdate'),
            $type::make('hsgrad')
                ->label('High School Grad'),
            $type::make('created_at'),
            $type::make('assigned_to.name'),
            $type::make('created_by.name'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your prospect report export has completed and ' . number_format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
        }

        return $body;
    }
}
