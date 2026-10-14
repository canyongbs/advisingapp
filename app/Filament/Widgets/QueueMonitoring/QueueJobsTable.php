<?php

/*
<COPYRIGHT>

    Copyright © 2016-2026, Canyon GBS Inc. All rights reserved.

    Advising App® is licensed under the Elastic License 2.0. For more details,
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
    - Canyon GBS Inc. respects the intellectual property rights of others and expects the
      same in return. Canyon GBS® and Advising App® are registered trademarks of
      Canyon GBS Inc., and we are committed to enforcing and protecting our trademarks
      vigorously.
    - The software solution, including services, infrastructure, and code, is offered as a
      Software as a Service (SaaS) by Canyon GBS Inc.
    - Use of this software implies agreement to the license terms and conditions as stated
      in the Elastic License 2.0.

    For more information or inquiries please visit our website at
    https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

namespace App\Filament\Widgets\QueueMonitoring;

use App\Filament\Pages\QueueMonitoring;
use App\Filament\Widgets\QueueMonitoring\Actions\ViewQueueJobAction;
use App\Filament\Widgets\QueueMonitoring\Columns\JobStatusColumn;
use App\Filament\Widgets\QueueMonitoring\Filters\CreatedAfterFilter;
use App\Filament\Widgets\QueueMonitoring\Filters\QueueFilter;
use App\Filament\Widgets\QueueMonitoring\Filters\TenantFilter;
use App\Models\Tenant;
use Cbox\LaravelQueueMonitor\Enums\JobStatus;
use Cbox\LaravelQueueMonitor\Models\JobMonitor;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\PaginationMode;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;

class QueueJobsTable extends TableWidget
{
    protected static bool $isDiscovered = false;

    protected static ?string $heading = 'Queue Jobs';

    protected int | string | array $columnSpan = 'full';

    public static function canView(): bool
    {
        return QueueMonitoring::canAccess();
    }

    public function table(Table $table): Table
    {
        $tenantNames = Tenant::query()->pluck('name', 'id')->all();

        return $table
            ->query(JobMonitor::query())
            ->defaultSort('created_at', 'desc')
            // The monitor holds a row per job attempt, so a count on every render is too slow.
            ->paginationMode(PaginationMode::Simple)
            ->columns([
                TextColumn::make('display_name')
                    ->label('Job')
                    ->state(fn (JobMonitor $record): string => $record->display_name ?? $record->job_class)
                    ->searchable(['display_name', 'job_class'])
                    ->limit(50),
                TextColumn::make('tenant_id')
                    ->label('Tenant')
                    ->state(fn (JobMonitor $record): string => $tenantNames[$record->getAttribute('tenant_id')] ?? ($record->getAttribute('tenant_id') === null ? 'Landlord' : 'Deleted tenant')),
                TextColumn::make('queue')
                    ->sortable(),
                JobStatusColumn::make(),
                TextColumn::make('attempt')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('duration_ms')
                    ->label('Duration')
                    ->placeholder('—')
                    ->formatStateUsing(fn (int $state): string => number_format($state) . ' ms')
                    ->sortable(),
                TextColumn::make('exception_message')
                    ->label('Exception')
                    ->placeholder('—')
                    ->limit(50)
                    ->toggleable(),
                TextColumn::make('completed_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options(collect(JobStatus::cases())->mapWithKeys(fn (JobStatus $status): array => [$status->value => $status->label()])->all()),
                QueueFilter::make(),
                TenantFilter::make($tenantNames),
                CreatedAfterFilter::make(),
            ])
            ->recordActions([
                ViewQueueJobAction::make($tenantNames),
            ]);
    }
}
