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

namespace AdvisingApp\StudentDataModel\Filament\Resources\StudentResource\Actions;

use AdvisingApp\StudentDataModel\Models\Student;
use App\Enums\TagType;
use App\Models\Tag;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Support\Enums\MaxWidth;

class StudentTagsAction extends Action
{
    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->modalHeading('Student Tags')
            ->modalWidth(MaxWidth::ExtraLarge)
            ->modalSubmitActionLabel('Save')
            ->form([
                Select::make('tag_ids')
                    ->options(fn (): array => Tag::where('type', TagType::Student)->pluck('name', 'id')->toArray())
                    ->required()
                    ->label('Tag')
                    ->multiple()
                    ->required()
                    ->default(fn (?Student $record): array => $record ? $record->tags->pluck('id')->toArray() : [])
                    ->searchable(),
            ])
            ->action(function (array $data, Student $record) {
                $record->tags()->sync($data['tag_ids']);
                $record->save();

                Notification::make()
                    ->title('Tags succesfully modified.')
                    ->success()
                    ->send();
            });
    }

    public static function getDefaultName(): ?string
    {
        return 'Tags';
    }
}
