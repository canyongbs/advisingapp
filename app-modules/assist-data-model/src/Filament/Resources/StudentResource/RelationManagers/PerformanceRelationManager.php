<?php

namespace Assist\AssistDataModel\Filament\Resources\StudentResource\RelationManagers;

use Filament\Tables;
use Filament\Tables\Table;
use App\Filament\Resources\RelationManagers\RelationManager;

class PerformanceRelationManager extends RelationManager
{
    protected static string $relationship = 'performances';

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('acad_career')
            ->columns([
                Tables\Columns\TextColumn::make('acad_career')
                    ->label('Academic Career'),
                Tables\Columns\TextColumn::make('division')
                    ->label('College'),
                Tables\Columns\IconColumn::make('first_gen')
                    ->label('First Gen')
                    ->boolean(),
                Tables\Columns\TextColumn::make('cum_att')
                    ->label('Cumulative Attempted'),
                Tables\Columns\TextColumn::make('cum_ern')
                    ->label('Cumulative Earned'),
                Tables\Columns\TextColumn::make('pct_ern')
                    ->label('Percent Earned'),
                Tables\Columns\TextColumn::make('cum_gpa')
                    ->label('Cumulative GPA'),
            ])
            ->filters([
            ])
            ->headerActions([])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([])
            ->emptyStateActions([]);
    }
}
