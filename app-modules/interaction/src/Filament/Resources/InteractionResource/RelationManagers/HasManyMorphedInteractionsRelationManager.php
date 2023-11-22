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

namespace Assist\Interaction\Filament\Resources\InteractionResource\RelationManagers;

use Filament\Tables\Table;
use Carbon\CarbonInterface;
use Filament\Infolists\Infolist;
use App\Filament\Columns\IdColumn;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Actions\CreateAction;
use Filament\Infolists\Components\Fieldset;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\RelationManagers\RelationManager;

class HasManyMorphedInteractionsRelationManager extends RelationManager
{
    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                TextEntry::make('user.name')
                    ->label('Created By'),
                Fieldset::make('Details')
                    ->schema([
                        TextEntry::make('campaign.name'),
                        TextEntry::make('driver.name'),
                        TextEntry::make('division.name'),
                        TextEntry::make('outcome.name'),
                        TextEntry::make('relation.name'),
                        TextEntry::make('status.name'),
                        TextEntry::make('type.name'),
                    ]),
                Fieldset::make('Time')
                    ->schema([
                        TextEntry::make('start_datetime')
                            ->label('Start Time')
                            ->dateTime(),
                        TextEntry::make('end_datetime')
                            ->label('End Time')
                            ->dateTime(),
                        TextEntry::make('start_datetime')
                            ->label('Duration')
                            ->state(fn ($record) => $record->end_datetime->diffForHumans($record->start_datetime, CarbonInterface::DIFF_ABSOLUTE, true, 6)),
                    ]),
                Fieldset::make('Notes')
                    ->schema([
                        TextEntry::make('subject'),
                        TextEntry::make('description'),
                    ]),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->columns([
                IdColumn::make(),
                TextColumn::make('campaign.name'),
                TextColumn::make('driver.name'),
                TextColumn::make('division.name'),
                TextColumn::make('outcome.name'),
                TextColumn::make('relation.name'),
                TextColumn::make('status.name'),
                TextColumn::make('type.name'),
                TextColumn::make('start_datetime')
                    ->label('Start Time')
                    ->dateTime(),
                TextColumn::make('end_datetime')
                    ->label('End Time')
                    ->dateTime(),
                TextColumn::make('created_at')
                    ->state(fn ($record) => $record->end_datetime->diffForHumans($record->start_datetime, CarbonInterface::DIFF_ABSOLUTE, true, 6))
                    ->label('Duration'),
                TextColumn::make('subject'),
                TextColumn::make('description'),
            ])
            ->filters([
            ])
            ->headerActions([
                CreateAction::make(),
            ])
            ->actions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->bulkActions([
            ]);
    }
}
