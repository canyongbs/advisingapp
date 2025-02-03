<?php

namespace AdvisingApp\StudentDataModel\Filament\Imports;

use AdvisingApp\StudentDataModel\Filament\Resources\StudentResource\Pages\Concerns\ImportColumns;
use AdvisingApp\StudentDataModel\Models\Program;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;

class ProgramImporter extends Importer
{
    use ImportColumns;

    protected static ?string $model = Program::class;

    public static function getColumns(): array
    {
        return self::getProgramColumns();
    }

    public function resolveRecord(): ?Program
    {
        $program = new Program();
        $program->student()->associate($this->options['sisid']);

        return $program;
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Your program import has completed and ' . number_format($import->successful_rows) . ' ' . str('row')->plural($import->successful_rows) . ' imported.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to import.';
        }

        return $body;
    }
}
