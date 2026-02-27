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

namespace AdvisingApp\StudentDataModel\Filament\Actions;

use AdvisingApp\StudentDataModel\Models\Student;
use App\Enums\TagType;
use App\Models\Tag;
use Filament\Actions\BulkAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class StudentTagsBulkAction
{
    public static function make(): BulkAction
    {
        return BulkAction::make('bulkStudentTags')
            ->icon('heroicon-o-tag')
            ->modalHeading('Bulk assign student tags')
            ->label('Manage Tags')
            ->modalDescription(
                fn (Collection $records) => "You have selected {$records->count()} " . Str::plural('student', $records->count()) . ' to apply tags.'
            )
            ->form([
                Select::make('tag_ids')
                    ->label('Which tags should be applied?')
                    ->options(
                        fn (): array => Tag::where('type', TagType::Student)
                            ->orderBy('name', 'ASC')
                            ->pluck('name', 'id')
                            ->toArray()
                    )
                    ->multiple()
                    ->searchable()
                    ->required()
                    ->exists('tags', 'id'),
                Toggle::make('remove_prior')
                    ->label('Remove all previously assigned tags?')
                    ->default(false)
                    ->hintIconTooltip('If checked, all prior tags assignments will be removed.'),
            ])
            ->action(function (array $data, Collection $records) {
                $records->each(function (Student $record) use ($data) {
                    if (! empty($data['tag_ids'])) {
                        $record->tags()
                            ->sync(
                                ids: $data['tag_ids'],
                                detaching: $data['remove_prior']
                            );
                    }
                });

                Notification::make()
                    ->title('Tags assigned successfully.')
                    ->success()
                    ->send();
            })
            ->deselectRecordsAfterCompletion();
    }
}
