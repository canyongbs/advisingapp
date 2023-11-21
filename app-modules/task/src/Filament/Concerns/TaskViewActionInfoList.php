<?php

/*
<COPYRIGHT>

Copyright © 2022-2023, Canyon GBS LLC

All rights reserved.

This file is part of a project developed using Laravel, which is an open-source framework for PHP.
Canyon GBS LLC acknowledges and respects the copyright of Laravel and other open-source
projects used in the development of this solution.

This project is licensed under the Affero General Public License (AGPL) 3.0.
For more details, see https://github.com/canyongbs/assistbycanyongbs/blob/main/LICENSE.

Notice:
- The copyright notice in this file and across all files and applications in this
 repository cannot be removed or altered without violating the terms of the AGPL 3.0 License.
- The software solution, including services, infrastructure, and code, is offered as a
 Software as a Service (SaaS) by Canyon GBS LLC.
- Use of this software implies agreement to the license terms and conditions as stated
 in the AGPL 3.0 License.

For more information or inquiries please visit our website at
https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

namespace Assist\Task\Filament\Concerns;

use Assist\Task\Models\Task;
use Assist\Task\Enums\TaskStatus;
use Assist\Prospect\Models\Prospect;
use Filament\Infolists\Components\Grid;
use App\Filament\Resources\UserResource;
use Filament\Infolists\Components\Split;
use Assist\AssistDataModel\Models\Student;
use Filament\Infolists\Components\Fieldset;
use Filament\Infolists\Components\TextEntry;
use Assist\Prospect\Filament\Resources\ProspectResource;
use Assist\AssistDataModel\Filament\Resources\StudentResource;

trait TaskViewActionInfoList
{
    public function taskInfoList(): array
    {
        return [
            Split::make([
                Grid::make()
                    ->schema([
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
                            ->getStateUsing(fn (Task $record): ?string => $record->concern?->{$record->concern::displayNameKey()})
                            ->url(fn (Task $record) => match ($record->concern ? $record->concern::class : null) {
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
                            ->badge()
                            ->color(fn (Task $record) => $record->status->getTableColor()),
                        TextEntry::make('due')
                            ->label('Due Date')
                            ->default('N/A'),
                        TextEntry::make('createdBy.name')
                            ->label('Created By')
                            ->default('N/A')
                            ->url(fn (Task $record) => $record->createdBy ? UserResource::getUrl('view', ['record' => $record->createdBy]) : null),
                    ]),
            ])->from('md'),
        ];
    }
}
