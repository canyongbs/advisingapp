<?php

namespace AdvisingApp\EnrollmentRecordManager\Filament\Imports;

use AdvisingApp\EnrollmentRecordManager\Models\ManageableEnrollment;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;

class ManageableEnrollmentImporter extends Importer
{
    protected static ?string $model = ManageableEnrollment::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('sisid')
                ->label('Student ID')
                ->requiredMapping()
                ->example('########')
                ->numeric(),
            ImportColumn::make('division')
                ->example('ABC01')
                ->label('Division'),
            ImportColumn::make('class_nbr')
                ->label('Class NBR')
                ->example('19309')
                ->numeric(),
            ImportColumn::make('crse_grade_off')
                ->example('A')
                ->label('CRSE grade off'),
            ImportColumn::make('unt_taken')
                ->label('UNT taken')
                ->example('1')
                ->numeric(),
            ImportColumn::make('unt_earned')
                ->label('UNT earned')
                ->example('1')
                ->numeric(),
            ImportColumn::make('last_upd_dt_stmp')
                ->label('Last UPD date STMP')
                ->example('1995-02-11 14:01:12+00'),
            ImportColumn::make('section')
                ->label('Section')
                ->example('7661')
                ->numeric(),
            ImportColumn::make('name')
                ->label('Name')
                ->example('Introduction to Mathematics'),
            ImportColumn::make('department')
                ->label('Department')
                ->example('Business'),
            ImportColumn::make('faculty_name')
                ->label('Faculty name')
                ->example('Keyon Metz'),
            ImportColumn::make('faculty_email')
                ->label('Faculty email')
                ->example('jerry72@example.net'),
            ImportColumn::make('semester_code')
                ->label('Semester code')
                ->example('4209'),
            ImportColumn::make('semester_name')
                ->label('Semester name')
                ->example('Fall 2006'),
            ImportColumn::make('start_date')
                ->label('Start date')
                ->example('2001-09-30 19:55:54'),
            ImportColumn::make('end_date')
                ->label('End date')
                ->example('2001-09-30 19:55:54'),
        ];
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Your manageable enrollment import has completed and ' . number_format($import->successful_rows) . ' ' . str('row')->plural($import->successful_rows) . ' imported.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to import.';
        }

        return $body;
    }
}
