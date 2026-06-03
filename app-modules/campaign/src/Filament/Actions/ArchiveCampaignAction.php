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
      of the licensor in the software. Any use of the licensor's trademarks is subject
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

namespace AdvisingApp\Campaign\Filament\Actions;

use AdvisingApp\Campaign\Models\Campaign;
use CanyonGBS\Common\Filament\Actions\ArchiveAction;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Filament\Resources\Pages\Page;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Throwable;

class ArchiveCampaignAction
{
    public static function make(): Action
    {
        return ArchiveAction::make()
            ->label(fn (Campaign $record): string => $record->enabled ? 'Disable and Archive' : 'Archive')
            ->modalHeading(fn (Campaign $record): string => $record->enabled ? 'Disable and Archive Campaign' : 'Archive Campaign')
            ->modalSubmitActionLabel(fn (Campaign $record): string => $record->enabled ? 'Disable and Archive' : 'Archive')
            ->action(function (Campaign $record): void {
                try {
                    DB::transaction(function () use ($record) {
                        if ($record->enabled) {
                            $record->update(['enabled' => false]);
                        }
                        $record->archive();
                    });

                    Notification::make()
                        ->success()
                        ->title('Campaign archived successfully')
                        ->send();
                } catch (Throwable $exception) {
                    report($exception);

                    Notification::make()
                        ->danger()
                        ->title('Failed to archive campaign')
                        ->body($exception->getMessage())
                        ->send();
                }
            })
            ->authorize(fn (Model $record, Page $livewire): bool => $livewire::getResource()::can('archive', $record))
            ->successRedirectUrl(function (Page $livewire): ?string {
                if ($livewire instanceof EditRecord) {
                    return $livewire::getResource()::getUrl('index');
                }

                return null;
            });
    }
}
