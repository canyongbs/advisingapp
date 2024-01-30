<?php

namespace AdvisingApp\Report\Filament\Exports;

use Filament\Actions\Exports\Exporter;
use Filament\Tables\Columns\TextColumn;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Models\Export;
use AdvisingApp\StudentDataModel\Models\Student;

class StudentExporter extends Exporter
{
    protected static ?string $model = Student::class;

    /**
     * @param class-string<TextColumn | ExportColumn> $type
     */
    public static function getColumns(string $type = ExportColumn::class): array
    {
        return [
            $type::make('sisid'),
            $type::make('otherid'),
            $type::make('first')
                ->label('First Name'),
            $type::make('last')
                ->label('Last Name'),
            $type::make('full_name')
                ->label('Full Name'),
            $type::make('preferred')
                ->label('Preferred Name'),
            $type::make('email')
                ->label('Email Address'),
            $type::make('email_2')
                ->label('Email Address 2'),
            $type::make('mobile'),
            $type::make('phone'),
            $type::make('address'),
            $type::make('address2')
                ->label('Address 2'),
            $type::make('address3')
                ->label('Address 3'),
            $type::make('city'),
            $type::make('state'),
            $type::make('postal'),
            $type::make('birthdate'),
            $type::make('hsgrad')
                ->label('High School Graduation'),
            $type::make('dfw')
                ->label('DFW'),
            $type::make('holds'),
            $type::make('ethnicity'),
            $type::make('lastlsmlogin')
                ->label('Last LMS Login'),
            $type::make('f_e_term')
                ->label('First Enrollment Term'),
            $type::make('mr_e_term')
                ->label('Most Recent Enrollment Term'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your student report export has completed and ' . number_format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
        }

        return $body;
    }
}
