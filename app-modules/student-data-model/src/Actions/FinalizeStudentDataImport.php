<?php

/*
<COPYRIGHT>

    Copyright © 2016-2025, Canyon GBS LLC. All rights reserved.

    Advising App™ is licensed under the Elastic License 2.0. For more details,
    see https://github.com/canyongbs/advisingapp/blob/main/LICENSE.

    Notice:

    - You may not provide the software to third parties as a hosted or managed
      service, where the service provides users with access to any substantial set of
      the features or functionality of the software.
    - You may not move, change, disable, or circumvent the license key functionality
      in the software, and you may not remove or obscure any functionality in the
      software that is protected by the license key.
    - You may not alter, remove, or obscure any licensing, copyright, or other notices
      of the licensor in the software. Any use of the licensor’s trademarks is subject
      to applicable law.
    - Canyon GBS LLC respects the intellectual property rights of others and expects the
      same in return. Canyon GBS™ and Advising App™ are registered trademarks of
      Canyon GBS LLC, and we are committed to enforcing and protecting our trademarks
      vigorously.
    - The software solution, including services, infrastructure, and code, is offered as a
      Software as a Service (SaaS) by Canyon GBS LLC.
    - Use of this software implies agreement to the license terms and conditions as stated
      in the Elastic License 2.0.

    For more information or inquiries please visit our website at
    https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

namespace AdvisingApp\StudentDataModel\Actions;

use App\Models\Import;
use Filament\Notifications\Actions\Action as NotificationAction;
use Filament\Notifications\Notification;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class FinalizeStudentDataImport
{
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

        if (! $totalFailedRowsCount) {
            DB::transaction(function () use ($studentsImport, $programsImport, $enrollmentsImport) {
                DB::statement('drop table "students"');
                DB::statement("alter table \"import_{$studentsImport->getKey()}_students\" rename to \"students\"");

                if ($programsImport) {
                    DB::statement('drop table "programs"');
                    DB::statement("alter table \"import_{$programsImport->getKey()}_programs\" rename to \"programs\"");
                }

                if ($enrollmentsImport) {
                    DB::statement('drop table "enrollments"');
                    DB::statement("alter table \"import_{$enrollmentsImport->getKey()}_enrollments\" rename to \"enrollments\"");
                }
            });
        } else {
            DB::statement("drop table if exists import_{$studentsImport->getKey()}_students");

            if ($programsImport) {
                DB::statement("drop table if exists import_{$programsImport->getKey()}_programs");
            }

            if ($enrollmentsImport) {
                DB::statement("drop table if exists import_{$enrollmentsImport->getKey()}_enrollments");
            }
        }

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
