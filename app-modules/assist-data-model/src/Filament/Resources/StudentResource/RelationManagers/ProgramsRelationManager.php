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

namespace Assist\AssistDataModel\Filament\Resources\StudentResource\RelationManagers;

use Filament\Tables\Table;
use Filament\Infolists\Infolist;
use App\Filament\Columns\IdColumn;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Infolists\Components\TextEntry;
use App\Filament\Resources\RelationManagers\RelationManager;

class ProgramsRelationManager extends RelationManager
{
    protected static string $relationship = 'programs';

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                TextEntry::make('sisid')
                    ->label('SISID'),
                TextEntry::make('otherid')
                    ->label('STUID'),
                TextEntry::make('division')
                    ->label('College'),
                TextEntry::make('descr')
                    ->label('Program'),
                TextEntry::make('foi')
                    ->label('Field of Interest'),
                TextEntry::make('cum_gpa')
                    ->label('Cumulative GPA'),
                TextEntry::make('declare_dt')
                    ->label('Start Date'),
                TextEntry::make('change_dt')
                    ->label('Last Action Date'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('descr')
            ->columns([
                IdColumn::make(),
                TextColumn::make('otherid')
                    ->label('STUID'),
                TextColumn::make('division')
                    ->label('College'),
                TextColumn::make('descr')
                    ->label('Program'),
                TextColumn::make('foi')
                    ->label('Field of Interest'),
                TextColumn::make('cum_gpa')
                    ->label('Cumulative GPA'),
                TextColumn::make('declare_dt')
                    ->label('Start Date'),
            ])
            ->filters([
            ])
            ->headerActions([])
            ->actions([
                ViewAction::make(),
            ])
            ->bulkActions([]);
    }
}
