<?php

namespace AdvisingApp\StudentDataModel\Actions;

use App\Models\Import;
use Filament\Notifications\Actions\Action as NotificationAction;
use Filament\Notifications\Notification;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class FinalizeStudentDataImport
{
    public function __construct(
        protected CleanUpFailedStudentDataImportTables $cleanUpFailedStudentDataImportTables,
    ) {}

    public function execute(
        Import $studentsImport,
        ?Import $programsImport = null,
        ?Import $enrollmentsImport = null,
    ): void {
        $studentsImport->touch('completed_at');
        $programsImport?->touch('completed_at');
        $enrollmentsImport?->touch('completed_at');

        $studentsImportFailedRowsCount = $studentsImport->getFailedRowsCount();
        $programsImportFailedRowsCount = $programsImport?->getFailedRowsCount() ?? 0;
        $enrollmentsImportFailedRowsCount = $enrollmentsImport?->getFailedRowsCount() ?? 0;

        $totalFailedRowsCount = $studentsImportFailedRowsCount + $programsImportFailedRowsCount + $enrollmentsImportFailedRowsCount;

        // if (! $totalFailedRowsCount) {
        //     DB::transaction(function () use ($studentsImport, $programsImport, $enrollmentsImport) {
        //         DB::statement('drop table "students"');
        //         DB::statement("alter table \"import_{$studentsImport->getKey()}_students\" rename to \"students\"");

        //         if ($programsImport) {
        //             DB::statement('drop table "programs"');
        //             DB::statement("alter table \"import_{$programsImport->getKey()}_programs\" rename to \"programs\"");
        //         }

        //         if ($enrollmentsImport) {
        //             DB::statement('drop table "enrollments"');
        //             DB::statement("alter table \"import_{$enrollmentsImport->getKey()}_enrollments\" rename to \"enrollments\"");
        //         }
        //     });
        // } else {
        //     $this->cleanUpFailedStudentDataImportTables->execute($studentsImport, $programsImport, $enrollmentsImport);
        // }

        if (! $studentsImport->user instanceof Authenticatable) {
            return;
        }

        if ($totalFailedRowsCount) {
            $body = 'Your student data import has finished, but there were issues so nothing was saved.';
            $actions = [];

            if ($studentsImportFailedRowsCount) {
                $body .= ' ' . number_format($studentsImportFailedRowsCount) . ' ' . Str::plural('student', $studentsImportFailedRowsCount) . ' failed to import.';
                $actions[] = NotificationAction::make('downloadFailedRowsCsv')
                    ->label('Download failed student data')
                    ->color('danger')
                    ->url(route('filament.imports.failed-rows.download', ['import' => $studentsImport], absolute: false), shouldOpenInNewTab: true)
                    ->markAsRead();
            }

            if ($programsImportFailedRowsCount) {
                $body .= ' ' . number_format($programsImportFailedRowsCount) . ' ' . Str::plural('program', $programsImportFailedRowsCount) . ' failed to import.';
                $actions[] = NotificationAction::make('downloadFailedProgramsRowsCsv')
                    ->label('Download failed program data')
                    ->color('danger')
                    ->url(route('filament.imports.failed-rows.download', ['import' => $programsImport], absolute: false), shouldOpenInNewTab: true)
                    ->markAsRead();
            }

            if ($enrollmentsImportFailedRowsCount) {
                $body .= ' ' . number_format($enrollmentsImportFailedRowsCount) . ' ' . Str::plural('enrollment', $enrollmentsImportFailedRowsCount) . ' failed to import.';
                $actions = NotificationAction::make('downloadFailedEnrollmentsRowsCsv')
                    ->label('Download failed enrollment data')
                    ->color('danger')
                    ->url(route('filament.imports.failed-rows.download', ['import' => $enrollmentsImport], absolute: false), shouldOpenInNewTab: true)
                    ->markAsRead();
            }

            Notification::make()
                ->title('Import failed')
                ->body($body)
                ->danger()
                ->actions($actions)
                ->sendToDatabase($studentsImport->user, isEventDispatched: true);

            return;
        }

        $body = 'Your student data import has completed and ' . number_format($studentsImport->successful_rows) . ' ' . Str::plural('student', $studentsImport->successful_rows) . ' imported.';

        if ($programsImport?->successful_rows) {
            $body .= ' ' . number_format($programsImport->successful_rows) . ' ' . Str::plural('program', $programsImport->successful_rows) . ' imported.';
        }

        if ($enrollmentsImport?->successful_rows) {
            $body .= ' ' . number_format($enrollmentsImport->successful_rows) . ' ' . Str::plural('enrollment', $enrollmentsImport->successful_rows) . ' imported.';
        }

        Notification::make()
            ->title('Import finished')
            ->body($body)
            ->success()
            ->sendToDatabase($studentsImport->user, isEventDispatched: true);
    }
}
