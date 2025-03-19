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

use AdvisingApp\StudentDataModel\Models\StudentDataImport;
use Filament\Notifications\Actions\Action as NotificationAction;
use Filament\Notifications\Notification;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CompleteStudentDataImport
{
    public function execute(
        StudentDataImport $import,
    ): void {
        $import->studentsImport->touch('completed_at');
        $import->emailAddressesImport?->touch('completed_at');
        $import->phoneNumbersImport?->touch('completed_at');
        $import->addressesImport?->touch('completed_at');
        $import->programsImport?->touch('completed_at');
        $import->enrollmentsImport?->touch('completed_at');

        $studentsImportFailedRowsCount = $import->studentsImport->getFailedRowsCount();
        $emailAddressesImportFailedRowsCount = $import->emailAddressesImport?->getFailedRowsCount() ?? 0;
        $phoneNumbersImportFailedRowsCount = $import->phoneNumbersImport?->getFailedRowsCount() ?? 0;
        $addressesImportFailedRowsCount = $import->addressesImport?->getFailedRowsCount() ?? 0;
        $programsImportFailedRowsCount = $import->programsImport?->getFailedRowsCount() ?? 0;
        $enrollmentsImportFailedRowsCount = $import->enrollmentsImport?->getFailedRowsCount() ?? 0;

        $totalFailedRowsCount = $studentsImportFailedRowsCount + $emailAddressesImportFailedRowsCount + $phoneNumbersImportFailedRowsCount + $addressesImportFailedRowsCount + $programsImportFailedRowsCount + $enrollmentsImportFailedRowsCount;

        if (! $totalFailedRowsCount) {
            DB::transaction(function () use ($import) {
                DB::table("import_{$import->studentsImport->getKey()}_students")
                    ->update([
                        'primary_email_id' => $import->emailAddressesImport ? DB::raw("(SELECT email.id FROM \"import_{$import->emailAddressesImport->getKey()}_email_addresses\" email WHERE email.sisid = \"import_{$import->studentsImport->getKey()}_students\".sisid AND email.\"order\" = 1 LIMIT 1)") : null,
                        'primary_phone_id' => $import->phoneNumbersImport ? DB::raw("(SELECT phone.id FROM \"import_{$import->phoneNumbersImport->getKey()}_phone_numbers\" phone WHERE phone.sisid = \"import_{$import->studentsImport->getKey()}_students\".sisid AND phone.\"order\" = 1 LIMIT 1)") : null,
                        'primary_address_id' => $import->addressesImport ? DB::raw("(SELECT address.id FROM \"import_{$import->addressesImport->getKey()}_addresses\" address WHERE address.sisid = \"import_{$import->studentsImport->getKey()}_students\".sisid AND address.\"order\" = 1 LIMIT 1)") : null,
                    ]);

                DB::statement('drop table "students"');
                DB::statement("alter table \"import_{$import->studentsImport->getKey()}_students\" rename to \"students\"");

                if ($import->emailAddressesImport) {
                    DB::statement('drop table "student_email_addresses"');
                    DB::statement("alter table \"import_{$import->emailAddressesImport->getKey()}_email_addresses\" rename to \"student_email_addresses\"");
                }

                if ($import->phoneNumbersImport) {
                    DB::statement('drop table "student_phone_numbers"');
                    DB::statement("alter table \"import_{$import->phoneNumbersImport->getKey()}_phone_numbers\" rename to \"student_phone_numbers\"");
                }

                if ($import->addressesImport) {
                    DB::statement('drop table "student_addresses"');
                    DB::statement("alter table \"import_{$import->addressesImport->getKey()}_addresses\" rename to \"student_addresses\"");
                }

                if ($import->programsImport) {
                    DB::statement('drop table "programs"');
                    DB::statement("alter table \"import_{$import->programsImport->getKey()}_programs\" rename to \"programs\"");
                }

                if ($import->enrollmentsImport) {
                    DB::statement('drop table "enrollments"');
                    DB::statement("alter table \"import_{$import->enrollmentsImport->getKey()}_enrollments\" rename to \"enrollments\"");
                }
            });
        } else {
            DB::statement("drop table if exists \"import_{$import->studentsImport->getKey()}_students\"");

            if ($import->emailAddressesImport) {
                DB::statement("drop table if exists \"import_{$import->emailAddressesImport->getKey()}_email_addresses\"");
            }

            if ($import->phoneNumbersImport) {
                DB::statement("drop table if exists \"import_{$import->phoneNumbersImport->getKey()}_phone_numbers\"");
            }

            if ($import->addressesImport) {
                DB::statement("drop table if exists \"import_{$import->addressesImport->getKey()}_addresses\"");
            }

            if ($import->programsImport) {
                DB::statement("drop table if exists \"import_{$import->programsImport->getKey()}_programs\"");
            }

            if ($import->enrollmentsImport) {
                DB::statement("drop table if exists \"import_{$import->enrollmentsImport->getKey()}_enrollments\"");
            }
        }

        if (! $import->studentsImport->user instanceof Authenticatable) {
            return;
        }

        if ($totalFailedRowsCount) {
            $import->canceled_at = now();
            $import->save();

            $body = 'Your student data import has finished, but there were issues so nothing was saved.';
            $actions = [];

            if ($studentsImportFailedRowsCount) {
                $body .= ' ' . number_format($studentsImportFailedRowsCount) . ' ' . Str::plural('student', $studentsImportFailedRowsCount) . ' failed to import.';
                $actions[] = NotificationAction::make('downloadFailedRowsCsv')
                    ->label('Download failed student data')
                    ->color('danger')
                    ->url(route('filament.imports.failed-rows.download', ['import' => $import->studentsImport], absolute: false), shouldOpenInNewTab: true)
                    ->markAsRead();
            }

            if ($emailAddressesImportFailedRowsCount) {
                $body .= ' ' . number_format($emailAddressesImportFailedRowsCount) . ' ' . Str::plural('email address', $emailAddressesImportFailedRowsCount) . ' failed to import.';
                $actions[] = NotificationAction::make('downloadFailedEmailAddressesRowsCsv')
                    ->label('Download failed email address data')
                    ->color('danger')
                    ->url(route('filament.imports.failed-rows.download', ['import' => $import->emailAddressesImport], absolute: false), shouldOpenInNewTab: true)
                    ->markAsRead();
            }

            if ($phoneNumbersImportFailedRowsCount) {
                $body .= ' ' . number_format($phoneNumbersImportFailedRowsCount) . ' ' . Str::plural('phone number', $phoneNumbersImportFailedRowsCount) . ' failed to import.';
                $actions[] = NotificationAction::make('downloadFailedPhoneNumbersRowsCsv')
                    ->label('Download failed phone number data')
                    ->color('danger')
                    ->url(route('filament.imports.failed-rows.download', ['import' => $import->phoneNumbersImport], absolute: false), shouldOpenInNewTab: true)
                    ->markAsRead();
            }

            if ($addressesImportFailedRowsCount) {
                $body .= ' ' . number_format($addressesImportFailedRowsCount) . ' ' . Str::plural('address', $addressesImportFailedRowsCount) . ' failed to import.';
                $actions[] = NotificationAction::make('downloadFailedAddressesRowsCsv')
                    ->label('Download failed address data')
                    ->color('danger')
                    ->url(route('filament.imports.failed-rows.download', ['import' => $import->addressesImport], absolute: false), shouldOpenInNewTab: true)
                    ->markAsRead();
            }

            if ($programsImportFailedRowsCount) {
                $body .= ' ' . number_format($programsImportFailedRowsCount) . ' ' . Str::plural('program', $programsImportFailedRowsCount) . ' failed to import.';
                $actions[] = NotificationAction::make('downloadFailedProgramsRowsCsv')
                    ->label('Download failed program data')
                    ->color('danger')
                    ->url(route('filament.imports.failed-rows.download', ['import' => $import->programsImport], absolute: false), shouldOpenInNewTab: true)
                    ->markAsRead();
            }

            if ($enrollmentsImportFailedRowsCount) {
                $body .= ' ' . number_format($enrollmentsImportFailedRowsCount) . ' ' . Str::plural('enrollment', $enrollmentsImportFailedRowsCount) . ' failed to import.';
                $actions = NotificationAction::make('downloadFailedEnrollmentsRowsCsv')
                    ->label('Download failed enrollment data')
                    ->color('danger')
                    ->url(route('filament.imports.failed-rows.download', ['import' => $import->enrollmentsImport], absolute: false), shouldOpenInNewTab: true)
                    ->markAsRead();
            }

            Notification::make()
                ->title('Import failed')
                ->body($body)
                ->danger()
                ->actions($actions)
                ->sendToDatabase($import->studentsImport->user, isEventDispatched: true);

            return;
        }

        $import->completed_at = now();
        $import->save();

        $body = 'Your student data import has completed and ' . number_format($import->studentsImport->successful_rows) . ' ' . Str::plural('student', $import->studentsImport->successful_rows) . ' imported.';

        if ($import->emailAddressesImport?->successful_rows) {
            $body .= ' ' . number_format($import->emailAddressesImport->successful_rows) . ' ' . Str::plural('email address', $import->emailAddressesImport->successful_rows) . ' imported.';
        }

        if ($import->phoneNumbersImport?->successful_rows) {
            $body .= ' ' . number_format($import->phoneNumbersImport->successful_rows) . ' ' . Str::plural('phone number', $import->phoneNumbersImport->successful_rows) . ' imported.';
        }

        if ($import->addressesImport?->successful_rows) {
            $body .= ' ' . number_format($import->addressesImport->successful_rows) . ' ' . Str::plural('address', $import->addressesImport->successful_rows) . ' imported.';
        }

        if ($import->programsImport?->successful_rows) {
            $body .= ' ' . number_format($import->programsImport->successful_rows) . ' ' . Str::plural('program', $import->programsImport->successful_rows) . ' imported.';
        }

        if ($import->enrollmentsImport?->successful_rows) {
            $body .= ' ' . number_format($import->enrollmentsImport->successful_rows) . ' ' . Str::plural('enrollment', $import->enrollmentsImport->successful_rows) . ' imported.';
        }

        Notification::make()
            ->title('Import finished')
            ->body($body)
            ->success()
            ->sendToDatabase($import->studentsImport->user, isEventDispatched: true);
    }
}
