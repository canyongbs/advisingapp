<?php

namespace Assist\CaseloadManagement\Importers;

use App\Models\Import;
use App\Imports\Importer;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Assist\AssistDataModel\Models\Student;
use App\Filament\Actions\ImportAction\ImportColumn;
use Assist\CaseloadManagement\Models\CaseloadSubject;

class StudentCaseloadSubjectImporter extends Importer
{
    protected static ?string $model = CaseloadSubject::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('subject')
                ->label('Student ID / Other ID')
                ->rules(['required'])
                ->relationship(
                    resolveUsing: fn (mixed $state) => Student::query()
                        ->where('sisid', $state)
                        ->orWhere('otherid', $state)
                        ->first(),
                )
                ->requiredMapping(),
        ];
    }

    public function resolveRecord(): ?Model
    {
        return new CaseloadSubject();
    }

    public function beforeCreate(): void
    {
        $this->record->caseload()->associate($this->getOptions()['caseload_id']);
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Your caseload import has completed and ' . number_format($import->successful_rows) . ' ' . Str::plural('student', $import->successful_rows) . ' imported.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . Str::plural('student', $failedRowsCount) . ' failed to import.';
        }

        return $body;
    }
}
