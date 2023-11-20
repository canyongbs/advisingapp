<?php

namespace Assist\CaseloadManagement\Importers;

use App\Models\Import;
use App\Imports\Importer;
use Illuminate\Support\Str;
use Assist\Prospect\Models\Prospect;
use Illuminate\Database\Eloquent\Model;
use App\Filament\Actions\ImportAction\ImportColumn;
use Assist\CaseloadManagement\Models\CaseloadSubject;

class ProspectCaseloadSubjectImporter extends Importer
{
    protected static ?string $model = CaseloadSubject::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('subject')
                ->label('Email address')
                ->rules(['required', 'email'])
                ->relationship(
                    resolveUsing: fn (mixed $state) => Prospect::query()
                        ->where('email', $state)
                        ->orWhere('email_2', $state)
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
        $body = 'Your caseload import has completed and ' . number_format($import->successful_rows) . ' ' . Str::plural('prospect', $import->successful_rows) . ' imported.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . Str::plural('prospect', $failedRowsCount) . ' failed to import.';
        }

        return $body;
    }
}
