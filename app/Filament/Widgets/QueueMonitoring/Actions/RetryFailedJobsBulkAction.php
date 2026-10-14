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

namespace App\Filament\Widgets\QueueMonitoring\Actions;

use App\Actions\QueueMonitoring\RetryFailedJobs;
use App\Models\FailedJob;
use App\Models\Tenant;
use Filament\Actions\BulkAction;
use Filament\Notifications\Notification;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class RetryFailedJobsBulkAction
{
    /**
     * @param  Tenant|null  $tenant  The tenant the failed jobs belong to, or null for the landlord.
     */
    public static function make(?Tenant $tenant): BulkAction
    {
        return BulkAction::make('retry')
            ->label('Retry selected')
            ->icon(Heroicon::ArrowPath)
            ->requiresConfirmation()
            ->action(function (Collection $records) use ($tenant): void {
                $uuids = $records->map(fn (FailedJob $record): string => $record->uuid)->values()->all();

                app(RetryFailedJobs::class)($uuids, $tenant);

                Log::info('Failed jobs retried from queue monitoring.', [
                    'uuids' => $uuids,
                    'tenant_id' => $tenant?->getKey(),
                    'user_id' => auth()->id(),
                ]);

                Notification::make()
                    ->title(count($uuids) . ' jobs queued for retry.')
                    ->success()
                    ->send();
            })
            ->deselectRecordsAfterCompletion();
    }
}
