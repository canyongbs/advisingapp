<?php

/*
<COPYRIGHT>

    Copyright © 2016-2024, Canyon GBS LLC. All rights reserved.

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

namespace AdvisingApp\Segment\Actions;

use Exception;
use Filament\Forms\Form;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use AdvisingApp\Segment\Models\Segment;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Actions\BulkAction;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use AdvisingApp\Segment\Enums\SegmentType;
use AdvisingApp\Segment\Enums\SegmentModel;

class BulkSegmentAction
{
    public static function make(SegmentModel $segmentModel): BulkAction
    {
        return BulkAction::make('segment')
            ->icon('heroicon-o-rectangle-group')
            ->label('Create Segment')
            ->form(fn (Form $form) => $form->schema([
                TextInput::make('name')
                    ->label('Name')
                    ->required()
                    ->maxLength(255),

                Textarea::make('description')
                    ->label('Description')
                    ->maxLength(65535),
            ]))
            ->action(function (Collection $records, array $data) use ($segmentModel) {
                try {
                    DB::beginTransaction();
                    $segment = Segment::create([
                        ...$data,
                        'type' => SegmentType::Static,
                        'filters' => [],
                        'model' => $segmentModel,
                    ]);
                    $records->chunk(100)->each(
                        fn ($chunkRecord) => $segment
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
                        ->title('Could not save segment')
                        ->body('We failed to create the segment. Please try again later.')
                        ->danger()
                        ->send();

                    return;
                }
                Notification::make()
                    ->title('Segment created')
                    ->body('The segment has been created and populated with your selections.')
                    ->success()
                    ->send();
            });
    }
}
