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

namespace App\Filament\Widgets\QueueMonitoring\Tables;

use App\Filament\Widgets\QueueMonitoring\Actions\ForgetFailedJobAction;
use App\Filament\Widgets\QueueMonitoring\Actions\RetryFailedJobAction;
use App\Filament\Widgets\QueueMonitoring\Actions\RetryFailedJobsBulkAction;
use App\Filament\Widgets\QueueMonitoring\Actions\ViewFailedJobAction;
use App\Models\FailedJob;
use App\Models\Tenant;
use Filament\Support\Enums\FontFamily;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\PaginationMode;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class FailedJobsTable
{
    /**
     * @param  Tenant|null  $tenant  The tenant whose failed jobs are listed, or null for the landlord's.
     */
    public static function configure(Table $table, ?Tenant $tenant): Table
    {
        return $table
            ->defaultSort('failed_at', 'desc')
            // failed_jobs can grow very large during an incident, exactly when this table must stay responsive.
            ->paginationMode(PaginationMode::Simple)
            ->columns([
                TextColumn::make('display_name')
                    ->label('Job'),
                TextColumn::make('uuid')
                    ->label('UUID')
                    ->fontFamily(FontFamily::Mono)
                    ->copyable()
                    ->limit(13),
                TextColumn::make('queue')
                    ->sortable(),
                TextColumn::make('exception')
                    ->formatStateUsing(fn (string $state): string => Str::before($state, "\n"))
                    ->limit(70),
                TextColumn::make('failed_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->recordActions([
                ViewFailedJobAction::make(),
                RetryFailedJobAction::make($tenant),
                ForgetFailedJobAction::make(),
            ])
            ->toolbarActions([
                RetryFailedJobsBulkAction::make($tenant),
            ])
            ->recordTitle(fn (FailedJob $record): string => $record->display_name);
    }
}
