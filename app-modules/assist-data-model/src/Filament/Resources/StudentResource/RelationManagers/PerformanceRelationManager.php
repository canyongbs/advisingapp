<?php

namespace Assist\AssistDataModel\Filament\Resources\StudentResource\RelationManagers;

use Filament\Tables\Table;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use App\Filament\Resources\RelationManagers\RelationManager;

class PerformanceRelationManager extends RelationManager
{
    protected static string $relationship = 'performances';

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('acad_career')
            ->columns([
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
            ->filters([
            ])
            ->headerActions([])
            ->actions([
                ViewAction::make(),
            ])
            ->bulkActions([])
            ->emptyStateActions([]);
    }
}
