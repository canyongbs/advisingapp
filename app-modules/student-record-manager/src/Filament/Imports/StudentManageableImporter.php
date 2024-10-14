<?php

/*
<COPYRIGHT>

    Copyright © 2016-2024, Canyon GBS LLC. All rights reserved.

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

namespace AdvisingApp\StudentRecordManager\Filament\Imports;

use AdvisingApp\StudentDataModel\Models\Student;
use Illuminate\Support\Str;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Models\Import;

class StudentManageableImporter extends Importer
{
    protected static ?string $model = Student::class;

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
                ->example('##########')
                ->numeric(),
            ImportColumn::make('first')
                ->example('Jonathan'),
            ImportColumn::make('last')
                ->example('Smith'),
            ImportColumn::make('full_name')
                ->example('Jonathan Smith'),
            ImportColumn::make('preferred')
                ->example('John'),
            ImportColumn::make('birthdate')
                ->example('2024-10-21'),
            ImportColumn::make('hsgrad')
                ->rules(['min:1920', 'max:now()->addYears(25)->year'])
                ->example('1920'),
            ImportColumn::make('email')
                ->rules(['required', 'email'])
                ->example('johnsmith@gmail.com'),
            ImportColumn::make('email_2')
                ->rules(['email'])
                ->example('johnsmith@hotmail.com'),
            ImportColumn::make('mobile')
                ->example('+1 (555) 555-5555'),
            ImportColumn::make('phone')
                ->example('+1 (555) 555-5555'),
            ImportColumn::make('address')
                ->example('123 Main St.'),
            ImportColumn::make('address2')
                ->example('Apt. 1'),
            ImportColumn::make('address3')
                ->example('xyz'),
            ImportColumn::make('city')
                ->example('Los Angeles'),
            ImportColumn::make('state')
                ->example('california'),
            ImportColumn::make('postal')
                ->example('83412'),
            ImportColumn::make('sms_opt_out')
                ->label('SMS opt out')
                ->boolean()
                ->rules(['boolean'])
                ->example('no'),
            ImportColumn::make('email_bounce')
                ->boolean()
                ->rules(['boolean'])
                ->example('yes'),
            ImportColumn::make('dual')
                ->boolean()
                ->rules(['boolean'])
                ->example('yes'),
            ImportColumn::make('ferpa')
                ->label('FERPA')
                ->boolean()
                ->rules(['boolean'])
                ->example('yes'),
            ImportColumn::make('dfw')
                ->label('DFW')
                ->example('2024-10-21'),
            ImportColumn::make('sap')
                ->label('SAP')
                ->boolean()
                ->rules(['boolean'])
                ->example('yes'),
            ImportColumn::make('holds')
                ->rules(['regex: [A-Z]{5}'])
                ->example('UHIJN'),
            ImportColumn::make('firstgen')
                ->boolean()
                ->rules(['boolean'])
                ->example('yes'),
            ImportColumn::make('ethnicity'),
            ImportColumn::make('lastlmslogin')
                ->label('Last LMS login'),
            ImportColumn::make('f_e_term')
                ->label('First Enrollement Term')
                ->numeric(),
            ImportColumn::make('mr_e_term')
                ->label('Most Recent Enrollement Term')
                ->numeric(),
        ];
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Your student import has completed and ' . number_format($import->successful_rows) . ' ' . Str::plural('row', $import->successful_rows) . ' imported.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . Str::plural('row', $failedRowsCount) . ' failed to import.';
        }

        return $body;
    }
}
