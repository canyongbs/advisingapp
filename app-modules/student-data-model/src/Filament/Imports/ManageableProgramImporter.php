<?php

namespace AdvisingApp\StudentDataModel\Filament\Imports;

use AdvisingApp\StudentDataModel\Models\Program;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;

class ManageableProgramImporter extends Importer
{
    protected static ?string $model = Program::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('sisid')
                ->label('Student ID')
                ->requiredMapping()
                ->example('########')
                ->numeric(),
            ImportColumn::make('otherid')
                ->label('Other ID')
                ->requiredMapping()
                ->example('########')
                ->numeric(),
            ImportColumn::make('acad_career')
                ->requiredMapping()
                ->label('ACAD career')
                ->example('0804'),
            ImportColumn::make('division')
                ->example('ABC01')
                ->requiredMapping(),
            ImportColumn::make('acad_plan')
                ->requiredMapping()
                ->label('ACAD plan'),
            ImportColumn::make('prog_status')
                ->requiredMapping()
                ->label('PROG status')
                ->example('AC'),
            ImportColumn::make('cum_gpa')
                ->requiredMapping()
                ->label('Cum GPA')
                ->numeric()
                ->example('3.284'),
            ImportColumn::make('semester')
                ->requiredMapping()
                ->numeric()
                ->example('1234'),
            ImportColumn::make('descr')
                ->requiredMapping()
                ->label('DESCR')
                ->numeric()
                ->example('Loream ipsum'),
            ImportColumn::make('foi')
                ->requiredMapping()
                ->label('Field of interest'),
            ImportColumn::make('change_dt')
                ->requiredMapping()
                ->label('Change date')
                ->example('1986-06-13 08:11:35+00'),
            ImportColumn::make('declare_dt')
                ->requiredMapping()
                ->label('Declare date')
                ->example('1986-06-13 08:11:35+00'),
        ];
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Your manageable program import has completed and ' . number_format($import->successful_rows) . ' ' . str('row')->plural($import->successful_rows) . ' imported.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to import.';
        }

        return $body;
    }
}
