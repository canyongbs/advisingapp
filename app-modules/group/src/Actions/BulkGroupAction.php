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

namespace AdvisingApp\Group\Actions;

use AdvisingApp\Group\Enums\GroupModel;
use AdvisingApp\Group\Enums\GroupType;
use AdvisingApp\Group\Models\Group;
use Exception;
use Filament\Actions\BulkAction;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Schemas\Schema;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class BulkGroupAction
{
    public static function make(GroupModel $groupModel): BulkAction
    {
        return BulkAction::make('segment')
            ->icon('heroicon-o-rectangle-group')
            ->label('Save Group')
            ->form(fn (Schema $schema) => $schema->components([
                TextInput::make('name')
                    ->label('Name')
                    ->required()
                    ->maxLength(255),

                Textarea::make('description')
                    ->label('Description')
                    ->maxLength(65535),
            ]))
            ->action(function (Collection $records, array $data) use ($groupModel) {
                try {
                    DB::beginTransaction();
                    $group = Group::create([
                        ...$data,
                        'type' => GroupType::Static,
                        'filters' => [],
                        'model' => $groupModel,
                    ]);
                    $records->chunk(100)->each(
                        fn ($chunkRecord) => $group
                            ->subjects()
                            ->createMany(
                                $chunkRecord->map(fn ($record) => [
                                    'subject_id' => $record->getKey(),
                                    'subject_type' => $record->getMorphClass(),
                                ])->toArray()
                            )
                    );
                    DB::commit();
                } catch (Exception $e) {
                    DB::rollBack();
                    Notification::make()
                        ->title('Could not save group')
                        ->body('We failed to create the group. Please try again later.')
                        ->danger()
                        ->send();

                    return;
                }
                Notification::make()
                    ->title('group created')
                    ->body('The group has been created and populated with your selections.')
                    ->success()
                    ->send();
            });
    }
}
