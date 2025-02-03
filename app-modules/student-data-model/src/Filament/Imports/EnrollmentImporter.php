<?php

namespace AdvisingApp\StudentDataModel\Filament\Imports;

use AdvisingApp\StudentDataModel\Filament\Resources\StudentResource\Pages\Concerns\ImportColumns;
use AdvisingApp\StudentDataModel\Models\Enrollment;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;

class EnrollmentImporter extends Importer
{
    use ImportColumns;

    protected static ?string $model = Enrollment::class;

    public static function getColumns(): array
    {
        return self::getEnrollmentColumns();
    }

    public function resolveRecord(): ?Enrollment
    {
        $enrollment = new Enrollment();
        $enrollment->student()->associate($this->options['sisid']);

        return $enrollment;
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Your enrollment import has completed and ' . number_format($import->successful_rows) . ' ' . str('row')->plural($import->successful_rows) . ' imported.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to import.';
        }

        return $body;
    }
}
