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

namespace AdvisingApp\StudentDataModel\Livewire;

use AdvisingApp\StudentDataModel\DataTransferObjects\StudentDataImportProgress;
use AdvisingApp\StudentDataModel\Enums\StudentDataImportStatus;
use AdvisingApp\StudentDataModel\Filament\Actions\ImportStudentDataAction;
use AdvisingApp\StudentDataModel\Filament\Tables\Columns\StudentDataImportProgressColumn;
use AdvisingApp\StudentDataModel\Models\StudentDataImport;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Component;

class StudentDataImportsTable extends Component implements HasActions, HasForms, HasTable
{
    use InteractsWithActions;
    use InteractsWithForms;
    use InteractsWithTable;

    public function table(Table $table): Table
    {
        return $table
            ->query(fn (): Builder => StudentDataImport::query()->latest())
            ->headerActions([
                ImportStudentDataAction::make(),
            ])
            ->columns([
                TextColumn::make('user.name'),
                TextColumn::make('status')
                    ->state(fn (StudentDataImport $record): StudentDataImportStatus => $record->getStatus())
                    ->badge(),
                StudentDataImportProgressColumn::make('students')
                    ->state(fn (StudentDataImport $record): ?StudentDataImportProgress => $record->studentsImport->total_rows ? new StudentDataImportProgress(
                        processed: $record->studentsImport->processed_rows,
                        total: $record->studentsImport->total_rows,
                        successful: $record->studentsImport->successful_rows,
                        failedRowsCsvUrl: route('filament.imports.failed-rows.download', ['import' => $record->studentsImport]),
                    ) : null),
                StudentDataImportProgressColumn::make('emailAddresses')
                    ->state(fn (StudentDataImport $record): ?StudentDataImportProgress => $record->emailAddressesImport?->total_rows ? new StudentDataImportProgress(
                        processed: $record->emailAddressesImport->processed_rows,
                        total: $record->emailAddressesImport->total_rows,
                        successful: $record->emailAddressesImport->successful_rows,
                        failedRowsCsvUrl: route('filament.imports.failed-rows.download', ['import' => $record->emailAddressesImport]),
                    ) : null),
                StudentDataImportProgressColumn::make('phoneNumbers')
                    ->state(fn (StudentDataImport $record): ?StudentDataImportProgress => $record->phoneNumbersImport?->total_rows ? new StudentDataImportProgress(
                        processed: $record->phoneNumbersImport->processed_rows,
                        total: $record->phoneNumbersImport->total_rows,
                        successful: $record->phoneNumbersImport->successful_rows,
                        failedRowsCsvUrl: route('filament.imports.failed-rows.download', ['import' => $record->phoneNumbersImport]),
                    ) : null),
                StudentDataImportProgressColumn::make('addresses')
                    ->state(fn (StudentDataImport $record): ?StudentDataImportProgress => $record->addressesImport?->total_rows ? new StudentDataImportProgress(
                        processed: $record->addressesImport->processed_rows,
                        total: $record->addressesImport->total_rows,
                        successful: $record->addressesImport->successful_rows,
                        failedRowsCsvUrl: route('filament.imports.failed-rows.download', ['import' => $record->addressesImport]),
                    ) : null),
                StudentDataImportProgressColumn::make('programs')
                    ->state(fn (StudentDataImport $record): ?StudentDataImportProgress => $record->programsImport?->total_rows ? new StudentDataImportProgress(
                        processed: $record->programsImport->processed_rows,
                        total: $record->programsImport->total_rows,
                        successful: $record->programsImport->successful_rows,
                        failedRowsCsvUrl: route('filament.imports.failed-rows.download', ['import' => $record->programsImport]),
                    ) : null),
                StudentDataImportProgressColumn::make('enrollments')
                    ->state(fn (StudentDataImport $record): ?StudentDataImportProgress => $record->enrollmentsImport?->total_rows ? new StudentDataImportProgress(
                        processed: $record->enrollmentsImport->processed_rows,
                        total: $record->enrollmentsImport->total_rows,
                        successful: $record->enrollmentsImport->successful_rows,
                        failedRowsCsvUrl: route('filament.imports.failed-rows.download', ['import' => $record->enrollmentsImport]),
                    ) : null),
                TextColumn::make('started_at')
                    ->label('Started')
                    ->placeholder('Pending start...')
                    ->dateTime()
                    ->sinceTooltip(),
                TextColumn::make('completed_at')
                    ->label('Completed')
                    ->dateTime()
                    ->sinceTooltip(),
                TextColumn::make('canceled_at')
                    ->label('Canceled')
                    ->dateTime()
                    ->sinceTooltip(),
                TextColumn::make('id')
                    ->label('ID')
                    ->copyable(),
            ])
            ->emptyStateHeading('A sync has not been run yet')
            ->poll();
    }

    public function render(): View
    {
        return view('student-data-model::livewire.student-data-imports-table');
    }
}
