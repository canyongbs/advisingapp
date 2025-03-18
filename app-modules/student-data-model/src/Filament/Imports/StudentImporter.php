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

namespace AdvisingApp\StudentDataModel\Filament\Imports;

use AdvisingApp\StudentDataModel\Models\Student;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Propaganistas\LaravelPhone\PhoneNumber;

class StudentImporter extends Importer
{
    protected static ?string $model = Student::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('sisid')
                ->label('Student ID')
                ->requiredMapping()
                ->example('########')
                ->rules([
                    'required',
                    'string',
                    'alpha_dash',
                    'max:255',
                ]),
            ImportColumn::make('otherid')
                ->label('Other ID')
                ->example('##########')
                ->rules([
                    'nullable',
                    'string',
                    'max:255',
                ]),
            ImportColumn::make('first')
                ->example('Jonathan')
                ->rules([
                    'nullable',
                    'string',
                    'max:255',
                ]),
            ImportColumn::make('last')
                ->example('Smith')
                ->rules([
                    'nullable',
                    'string',
                    'max:255',
                ]),
            ImportColumn::make('full_name')
                ->example('Jonathan Smith')
                ->rules([
                    'nullable',
                    'string',
                    'max:255',
                ]),
            ImportColumn::make('preferred')
                ->example('John')
                ->rules([
                    'nullable',
                    'string',
                    'max:255',
                ]),
            ImportColumn::make('birthdate')
                ->example('2024-10-21')
                ->rules([
                    'nullable',
                    'date',
                ]),
            ImportColumn::make('hsgrad')
                ->example('1920')
                ->rules([
                    'nullable',
                    'integer',
                ]),
            ImportColumn::make('sms_opt_out')
                ->label('SMS opt out')
                ->example('false')
                ->boolean(),
            ImportColumn::make('email_bounce')
                ->example('true')
                ->boolean(),
            ImportColumn::make('dual')
                ->example('true')
                ->boolean(),
            ImportColumn::make('ferpa')
                ->label('FERPA')
                ->example('true')
                ->boolean(),
            ImportColumn::make('dfw')
                ->label('DFW')
                ->example('2024-10-21')
                ->rules([
                    'nullable',
                    'date',
                ]),
            ImportColumn::make('sap')
                ->label('SAP')
                ->example('true')
                ->boolean(),
            ImportColumn::make('holds')
                ->example('UHIJN')
                ->rules([
                    'nullable',
                    'string',
                    'max:255',
                ]),
            ImportColumn::make('firstgen')
                ->example('true')
                ->boolean(),
            ImportColumn::make('ethnicity')
                ->rules([
                    'nullable',
                    'string',
                    'max:255',
                ]),
            ImportColumn::make('lastlmslogin')
                ->label('Last LMS login')
                ->example('2024-10-21 12:00:00')
                ->rules([
                    'nullable',
                    'date',
                ]),
            ImportColumn::make('f_e_term')
                ->label('First Enrollment Term')
                ->example('1234')
                ->rules([
                    'nullable',
                    'string',
                    'max:255',
                ]),
            ImportColumn::make('mr_e_term')
                ->label('Most Recent Enrollment Term')
                ->example('1234')
                ->rules([
                    'nullable',
                    'string',
                    'max:255',
                ]),
        ];
    }

    public function resolveRecord(): Student
    {
        return (new Student())->setTable("import_{$this->import->getKey()}_students");
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Your student import has completed and ' . number_format($import->successful_rows) . ' ' . Str::plural('row', $import->successful_rows) . ' imported.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . Str::plural('row', $failedRowsCount) . ' failed to import.';
        }

        return $body;
    }

    public function getJobBatchName(): ?string
    {
        return "student-import-{$this->getImport()->getKey()}";
    }
}
