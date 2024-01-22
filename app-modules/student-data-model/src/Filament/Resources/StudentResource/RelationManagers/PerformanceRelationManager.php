<?php

/*
<COPYRIGHT>

    Copyright © 2022-2023, Canyon GBS LLC. All rights reserved.

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

namespace AdvisingApp\StudentDataModel\Filament\Resources\StudentResource\RelationManagers;

use Filament\Tables\Table;
use Filament\Infolists\Infolist;
use App\Filament\Columns\IdColumn;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use App\Filament\Resources\RelationManagers\RelationManager;

class PerformanceRelationManager extends RelationManager
{
    protected static string $relationship = 'performances';

    protected static ?string $title = 'Performance';

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema(
                [
                    TextEntry::make('sisid')
                        ->label('SISID'),
                    TextEntry::make('acad_career')
                        ->label('Academic Career'),
                    TextEntry::make('division')
                        ->label('College'),
                    IconEntry::make('first_gen')
                        ->label('First Gen')
                        ->boolean(),
                    TextEntry::make('cum_att')
                        ->label('Cumulative Attempted'),
                    TextEntry::make('cum_ern')
                        ->label('Cumulative Earned'),
                    TextEntry::make('pct_ern')
                        ->label('Percent Earned'),
                    TextEntry::make('cum_gpa')
                        ->label('Cumulative GPA'),
                    TextEntry::make('max_dt')
                        ->label('Max Dt'),
                ]
            );
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('acad_career')
            ->columns([
                IdColumn::make(),
                TextColumn::make('acad_career')
                    ->label('Academic Career'),
                TextColumn::make('division')
                    ->label('College'),
                IconColumn::make('first_gen')
                    ->label('First Gen')
                    ->boolean(),
                TextColumn::make('cum_att')
                    ->label('Cumulative Attempted'),
                TextColumn::make('cum_ern')
                    ->label('Cumulative Earned'),
                TextColumn::make('pct_ern')
                    ->label('Percent Earned'),
                TextColumn::make('cum_gpa')
                    ->label('Cumulative GPA'),
            ])
            ->headerActions([])
            ->actions([
                ViewAction::make(),
            ])
            ->bulkActions([]);
    }
}
