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

use AdvisingApp\StudentDataModel\Models\StudentPhoneNumber;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;
use Illuminate\Support\Facades\DB;

class StudentPhoneNumberImporter extends Importer
{
    protected static ?string $model = StudentPhoneNumber::class;

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
            ImportColumn::make('number')
                ->rules(['max:255'])
                ->example('+1 (777) 777-7777'),
            ImportColumn::make('ext')
                ->label('Extension')
                ->rules(['integer', 'max_digits:8'])
                ->example('789'),
            ImportColumn::make('type')
                ->rules(['max:255'])
                ->example('Work'),
            ImportColumn::make('can_receive_sms')
                ->boolean()
                ->rules(['boolean'])
                ->example('false'),
            ImportColumn::make('is_primary')
                ->boolean()
                ->rules(['boolean'])
                ->example('false')
                ->fillRecordUsing(fn (StudentPhoneNumber $record, mixed $state) => $record->order = $state ? 1 : null),
        ];
    }

    public function resolveRecord(): StudentPhoneNumber
    {
        return (new StudentPhoneNumber())->setTable("import_{$this->import->getKey()}_phone_numbers");
    }

    public function afterCreate(): void
    {
        if ($this->data['is_primary'] ?? null) {
            DB::statement("
                with ordered_results as (
                    select 
                        id,
                        row_number() over (order by \"order\") as new_order
                    from \"{$this->record->getTable()}\"
                    where id != '{$this->record->getKey()}'
                )
                update \"{$this->record->getTable()}\"
                set \"order\" = ordered_results.new_order + 1
                from ordered_results
                where \"{$this->record->getTable()}\".id = ordered_results.id
            ");
        }
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Your phone number import has completed and ' . number_format($import->successful_rows) . ' ' . str('row')->plural($import->successful_rows) . ' imported.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to import.';
        }

        return $body;
    }
}
