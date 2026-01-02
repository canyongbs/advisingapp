<?php

/*
<COPYRIGHT>

    Copyright © 2016-2026, Canyon GBS LLC. All rights reserved.

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

namespace AdvisingApp\Concern\Filament\Actions;

use AdvisingApp\Concern\Enums\ConcernSeverity;
use AdvisingApp\Concern\Enums\SystemConcernStatusClassification;
use AdvisingApp\Concern\Models\ConcernStatus;
use AdvisingApp\Prospect\Models\Prospect;
use AdvisingApp\StudentDataModel\Models\Student;
use Exception;
use Filament\Actions\BulkAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class BulkCreateConcernAction
{
    public static function make(): BulkAction
    {
        return BulkAction::make('createConcern')
            ->label('Open Concern')
            ->icon('heroicon-o-bell-alert')
            ->modalHeading('Create Concern')
            ->form([
                Textarea::make('description')
                    ->required()
                    ->maxLength(65535)
                    ->string()
                    ->label('Description'),
                Select::make('severity')
                    ->options(ConcernSeverity::class)
                    ->default(ConcernSeverity::default())
                    ->required()
                    ->enum(ConcernSeverity::class)
                    ->label('Severity'),
                Textarea::make('suggested_intervention')
                    ->required()
                    ->maxLength(65535)
                    ->string()
                    ->label('Suggested Intervention'),
                Select::make('status_id')
                    ->label('Status')
                    ->options(ConcernStatus::orderBy('order')->pluck('name', 'id'))
                    ->default(fn () => SystemConcernStatusClassification::default()?->getKey())
                    ->exists('alert_statuses', 'id')
                    ->required(),
            ])->action(function (Collection $records, array $data) {
                try {
                    DB::beginTransaction();

                    foreach ($records as $record) {
                        throw_unless($record instanceof Student || $record instanceof Prospect, new Exception('Record must be of type student or prospect.'));
                        $record->concerns()->create([
                            ...$data,
                        ]);
                    }

                    DB::commit();
                } catch (Exception $exception) {
                    DB::rollBack();
                    Notification::make()
                        ->title('Something went wrong')
                        ->body('We failed to create the concerns. Please try again later.')
                        ->danger()
                        ->send();

                    return;
                }
                Notification::make()
                    ->title('Concerns created')
                    ->body('Concerns have been created with your selections.')
                    ->success()
                    ->send();
            })
            ->deselectRecordsAfterCompletion();
    }
}
