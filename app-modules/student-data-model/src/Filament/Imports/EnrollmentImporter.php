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

namespace AdvisingApp\StudentDataModel\Filament\Imports;

use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Models\Import;
use AdvisingApp\StudentDataModel\Models\Enrollment;

class EnrollmentImporter extends Importer
{
    protected static ?string $model = Enrollment::class;

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
                    'max:255',
                ]),
            ImportColumn::make('division')
                ->example('ABC01')
                ->label('Division')
                ->rules([
                    'nullable',
                    'string',
                    'max:255',
                ]),
            ImportColumn::make('class_nbr')
                ->label('Class NBR')
                ->example('19309')
                ->rules([
                    'nullable',
                    'string',
                    'max:255',
                ]),
            ImportColumn::make('crse_grade_off')
                ->example('A')
                ->label('CRSE grade off')
                ->rules([
                    'nullable',
                    'string',
                    'max:255',
                ]),
            ImportColumn::make('unt_taken')
                ->label('UNT taken')
                ->example('1')
                ->numeric()
                ->rules([
                    'nullable',
                    'numeric',
                ]),
            ImportColumn::make('unt_earned')
                ->label('UNT earned')
                ->example('1')
                ->numeric()
                ->rules([
                    'nullable',
                    'numeric',
                ]),
            ImportColumn::make('last_upd_dt_stmp')
                ->label('Last UPD date STMP')
                ->example('1995-02-11 14:01:12+00')
                ->rules([
                    'nullable',
                    'date',
                ]),
            ImportColumn::make('section')
                ->label('Section')
                ->example('7661')
                ->rules([
                    'nullable',
                    'string',
                    'max:255',
                ]),
            ImportColumn::make('name')
                ->label('Name')
                ->example('Introduction to Mathematics')
                ->rules([
                    'nullable',
                    'string',
                    'max:255',
                ]),
            ImportColumn::make('department')
                ->label('Department')
                ->example('Business')
                ->rules([
                    'nullable',
                    'string',
                    'max:255',
                ]),
            ImportColumn::make('faculty_name')
                ->label('Faculty name')
                ->example('Keyon Metz')
                ->rules([
                    'nullable',
                    'string',
                    'max:255',
                ]),
            ImportColumn::make('faculty_email')
                ->label('Faculty email')
                ->example('jerry72@example.net')
                ->rules([
                    'nullable',
                    'email',
                    'max:255',
                ]),
            ImportColumn::make('semester_code')
                ->label('Semester code')
                ->example('4209')
                ->rules([
                    'nullable',
                    'string',
                    'max:255',
                ]),
            ImportColumn::make('semester_name')
                ->label('Semester name')
                ->example('Fall 2006')
                ->rules([
                    'nullable',
                    'string',
                    'max:255',
                ]),
            ImportColumn::make('start_date')
                ->label('Start date')
                ->example('2001-09-30 19:55:54')
                ->rules([
                    'nullable',
                    'date',
                ]),
            ImportColumn::make('end_date')
                ->label('End date')
                ->example('2001-09-30 19:55:54')
                ->rules([
                    'nullable',
                    'date',
                ]),
        ];
    }

    public function resolveRecord(): ?Enrollment
    {
        return new Enrollment();
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
