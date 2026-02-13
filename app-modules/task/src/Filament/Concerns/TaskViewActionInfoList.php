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

namespace AdvisingApp\Task\Filament\Concerns;

use AdvisingApp\Prospect\Filament\Resources\Prospects\ProspectResource;
use AdvisingApp\Prospect\Models\Prospect;
use AdvisingApp\StudentDataModel\Filament\Resources\Students\StudentResource;
use AdvisingApp\StudentDataModel\Models\Student;
use AdvisingApp\Task\Enums\TaskStatus;
use AdvisingApp\Task\Models\Task;
use App\Filament\Resources\Users\UserResource;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Flex;
use Filament\Schemas\Components\Grid;

trait TaskViewActionInfoList
{
    /**
     * @return array<Flex>
     */
    public function taskInfoList(): array
    {
        return [
            Flex::make([
                Grid::make()
                    ->schema([
                        TextEntry::make('is_confidential')
                            ->columnSpanFull()
                            ->label('')
                            ->badge()
                            ->formatStateUsing(fn ($state): string => $state ? 'Confidential' : '')
                            ->visible(fn ($record): bool => $record->is_confidential),
                        TextEntry::make('title')
                            ->columnSpanFull(),
                        TextEntry::make('description')
                            ->columnSpanFull(),
                        TextEntry::make('assignedTo.name')
                            ->label('Assigned To')
                            ->url(fn (Task $record) => $record->assignedTo ? UserResource::getUrl('view', ['record' => $record->assignedTo]) : null)
                            ->default('Unassigned'),
                        TextEntry::make('concern.display_name')
                            ->label('Related To')
                            ->state(fn (Task $record): ?string => $record->concern->{$record->concern::displayNameKey()})
                            ->url(fn (Task $record) => match ($record->concern::class) {
                                Student::class => StudentResource::getUrl('view', ['record' => $record->concern]),
                                Prospect::class => ProspectResource::getUrl('view', ['record' => $record->concern]),
                                default => null,
                            })
                            ->default('Unrelated'),
                    ]),
                Fieldset::make('metadata')
                    ->label('Metadata')
                    ->schema([
                        TextEntry::make('status')
                            ->formatStateUsing(fn (TaskStatus $state): string => str($state->value)->title()->headline())
                            ->badge(),
                        TextEntry::make('due')
                            ->label('Due Date')
                            ->dateTime()
                            ->placeholder('N/A'),
                        TextEntry::make('createdBy.name')
                            ->label('Created By')
                            ->placeholder('N/A')
                            ->url(fn (Task $record) => $record->createdBy ? UserResource::getUrl('view', ['record' => $record->createdBy]) : null),
                    ]),
            ])->from('md'),
        ];
    }
}
