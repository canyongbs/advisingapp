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

namespace AdvisingApp\ResourceHub\Filament\Actions;

use AdvisingApp\ResourceHub\Models\ResourceHubArticle;
use App\Models\User;
use Exception;
use Filament\Actions\BulkAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AssignManagerBulkAction
{
    public static function make(): BulkAction
    {
        return BulkAction::make('bulkManagers')
            ->label('Assign Managers')
            ->icon('heroicon-s-user-group')
            ->modalHeading('Bulk Assign Managers')
            ->modalDescription(fn (Collection $records) => "You have selected {$records->count()} " . Str::plural('article', $records->count()) . ' to assign manager(s).')
            ->schema([
                Select::make('manager_ids')
                    ->label('Managers')
                    ->options(fn (): array => User::query()->limit(50)->pluck('name', 'id')->all())
                    ->multiple()
                    ->searchable()
                    ->preload()
                    ->exists('users', 'id'),
                Toggle::make('remove_prior')
                    ->label('Remove all previous assigned managers?')
                    ->default(false)
                    ->hintIconTooltip('If selected, all prior managers will be removed.'),
            ])
            ->action(function (array $data, Collection $records) {
                try {
                    DB::beginTransaction();

                    $records->each(function (ResourceHubArticle $record) use ($data) {
                        if (! empty($data['manager_ids'])) {
                            $record->managers()->sync($data['manager_ids'], $data['remove_prior']);
                        }
                    });

                    Notification::make()
                        ->title('Managers assigned successfully.')
                        ->success()
                        ->send();

                    DB::commit();
                } catch (Exception $exception) {
                    DB::rollBack();

                    Notification::make()
                        ->title('Something went wrong')
                        ->body('We failed to assign these managers. Please try again later.')
                        ->danger()
                        ->send();
                }
            })
            ->deselectRecordsAfterCompletion();
    }
}
