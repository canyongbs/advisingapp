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
use AdvisingApp\StudentDataModel\Models\Program;

class ProgramImporter extends Importer
{
    protected static ?string $model = Program::class;

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
            ImportColumn::make('otherid')
                ->label('Other ID')
                ->requiredMapping()
                ->example('########')
                ->rules([
                    'required',
                    'string',
                    'max:255',
                ]),
            ImportColumn::make('acad_career')
                ->requiredMapping()
                ->label('ACAD career')
                ->example('CRED')
                ->rules([
                    'required',
                    'string',
                    'max:255',
                ]),
            ImportColumn::make('division')
                ->example('ABC01')
                ->requiredMapping()
                ->example('ABC01')
                ->rules([
                    'required',
                    'string',
                    'max:255',
                ]),
            ImportColumn::make('acad_plan')
                ->requiredMapping()
                ->label('ACAD plan')
                ->example('1076N')
                ->rules([
                    'required',
                    'string',
                    'max:255',
                ]),
            ImportColumn::make('prog_status')
                ->requiredMapping()
                ->label('PROG status')
                ->example('AC')
                ->rules([
                    'required',
                    'string',
                    'max:255',
                ]),
            ImportColumn::make('cum_gpa')
                ->requiredMapping()
                ->label('Cum GPA')
                ->numeric()
                ->example('3.284')
                ->rules([
                    'required',
                    'numeric',
                ]),
            ImportColumn::make('semester')
                ->requiredMapping()
                ->example('1234')
                ->rules([
                    'required',
                    'string',
                    'max:255',
                ]),
            ImportColumn::make('descr')
                ->requiredMapping()
                ->label('DESCR')
                ->example('Loream ipsum')
                ->rules([
                    'required',
                    'string',
                    'max:255',
                ]),
            ImportColumn::make('foi')
                ->requiredMapping()
                ->label('Field of interest')
                ->rules([
                    'required',
                    'string',
                    'max:255',
                ]),
            ImportColumn::make('change_dt')
                ->requiredMapping()
                ->label('Change date')
                ->example('1986-06-13 08:11:35+00')
                ->rules([
                    'required',
                    'date',
                ]),
            ImportColumn::make('declare_dt')
                ->requiredMapping()
                ->label('Declare date')
                ->example('1986-06-13 08:11:35+00')
                ->rules([
                    'required',
                    'date',
                ]),
        ];
    }

    public function resolveRecord(): ?Program
    {
        return new Program();
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
