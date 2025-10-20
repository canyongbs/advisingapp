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

use AdvisingApp\StudentDataModel\DataTransferObjects\SisPipelineProgress;
use AdvisingApp\StudentDataModel\Enums\SisPipelineStatus;
use AdvisingApp\StudentDataModel\Filament\Tables\Columns\SisPipelineProgressColumn;
use App\Models\Tenant;
use App\Services\Olympus;
use Exception;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class SisDataPipelineTable extends Component implements HasActions, HasForms, HasTable
{
    use InteractsWithActions;
    use InteractsWithForms;
    use InteractsWithTable;

    public function table(Table $table): Table
    {
        return $table
            ->records(fn (): array => $this->fetchSisDataPipeline())
            ->columns([
                TextColumn::make('status')
                    ->state(fn (array $record): SisPipelineStatus => $this->getStatus($record))
                    ->badge(),
                SisPipelineProgressColumn::make('total')
                    ->label('Total')
                    ->state(fn (array $record): ?SisPipelineProgress => $this->getTotalProgress($record)),
                SisPipelineProgressColumn::make('students')
                    ->label('Students')
                    ->state(fn (array $record): ?SisPipelineProgress => $this->getStudentsProgress($record)),
                SisPipelineProgressColumn::make('enrollments')
                    ->label('Enrollments')
                    ->state(fn (array $record): ?SisPipelineProgress => $this->getEnrollmentsProgress($record)),
                SisPipelineProgressColumn::make('programs')
                    ->label('Programs')
                    ->state(fn (array $record): ?SisPipelineProgress => $this->getProgramsProgress($record)),
                TextColumn::make('type')
                    ->label('Type')
                    ->formatStateUsing(fn (string $state): string => str($state)->title()),
                TextColumn::make('trigger')
                    ->label('Trigger')
                    ->formatStateUsing(fn (string $state): string => str($state)->replace('_', ' ')->title()),
                TextColumn::make('started_at')
                    ->label('Started At')
                    ->dateTime(),
                TextColumn::make('completed_at')
                    ->label('Completed At')
                    ->dateTime(),
            ])
            ->defaultSort('started_at', 'desc')
            ->emptyStateHeading('A sync has not been run yet')
            ->poll();
    }

    public function render(): View
    {
        return view('student-data-model::livewire.sis-data-pipeline-table');
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function fetchSisDataPipeline(): array
    {
        try {
            $tenantId = Tenant::current()->getKey();

            $response = app(Olympus::class)->makeRequest()
                ->get("integrations/{$tenantId}/sis-sync-pipeline-data");

            if ($response->successful()) {
                $responseData = $response->json();
                $data = $responseData['data'] ?? [];

                return $data;
            }
        } catch (Exception $exception) {
            report($exception);
        }

        return [];
    }

    /**
     * @param array<string, mixed> $record
     */
    protected function getStatus(array $record): SisPipelineStatus
    {
        return match (true) {
            filled($record['canceled_at'] ?? null) => SisPipelineStatus::Canceled,
            filled($record['completed_at'] ?? null) => SisPipelineStatus::Completed,
            filled($record['started_at'] ?? null) => SisPipelineStatus::Processing,
            default => SisPipelineStatus::Pending,
        };
    }

    /**
     * @param array<string, mixed> $record
     */
    protected function getTotalProgress(array $record): ?SisPipelineProgress
    {
        $totalItems = ($record['total_student'] ?? 0) + ($record['total_enrollment'] ?? 0) + ($record['total_program'] ?? 0);

        $processedItems = ($record['processed_students'] ?? 0) + ($record['processed_enrollments'] ?? 0) + ($record['processed_programs'] ?? 0);

        $successfulItems = ($record['successful_students'] ?? 0) + ($record['successful_enrollments'] ?? 0) + ($record['successful_programs'] ?? 0);

        if ($totalItems === 0) {
            return null;
        }

        return new SisPipelineProgress(
            processed: $processedItems,
            total: $totalItems,
            successful: $successfulItems,
        );
    }

    /**
     * @param array<string, mixed> $record
     */
    protected function getStudentsProgress(array $record): ?SisPipelineProgress
    {
        if (($record['total_student'] ?? 0) === 0) {
            return null;
        }

        return new SisPipelineProgress(
            processed: $record['processed_students'] ?? 0,
            total: $record['total_student'] ?? 0,
            successful: $record['successful_students'] ?? 0,
        );
    }

    /**
     * @param array<string, mixed> $record
     */
    protected function getEnrollmentsProgress(array $record): ?SisPipelineProgress
    {
        if (($record['total_enrollment'] ?? 0) === 0) {
            return null;
        }

        return new SisPipelineProgress(
            processed: $record['processed_enrollments'] ?? 0,
            total: $record['total_enrollment'] ?? 0,
            successful: $record['successful_enrollments'] ?? 0,
        );
    }

    /**
     * @param array<string, mixed> $record
     */
    protected function getProgramsProgress(array $record): ?SisPipelineProgress
    {
        if (($record['total_program'] ?? 0) === 0) {
            return null;
        }

        return new SisPipelineProgress(
            processed: $record['processed_programs'] ?? 0,
            total: $record['total_program'] ?? 0,
            successful: $record['successful_programs'] ?? 0,
        );
    }
}
