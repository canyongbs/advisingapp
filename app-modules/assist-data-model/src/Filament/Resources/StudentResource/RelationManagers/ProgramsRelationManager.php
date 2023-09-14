<?php

namespace Assist\AssistDataModel\Filament\Resources\StudentResource\RelationManagers;

use Filament\Tables;
use Filament\Tables\Table;
use App\Filament\Resources\RelationManagers\RelationManager;

class ProgramsRelationManager extends RelationManager
{
    protected static string $relationship = 'programs';

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('descr')
            ->columns([
                Tables\Columns\TextColumn::make('otherid')
                    ->label('STUID'),
                Tables\Columns\TextColumn::make('division')
                    ->label('College'),
                Tables\Columns\TextColumn::make('descr')
                    ->label('Program'),
                Tables\Columns\TextColumn::make('foi')
                    ->label('Field of Interest'),
                Tables\Columns\TextColumn::make('cum_gpa')
                    ->label('Cumulative GPA'),
                Tables\Columns\TextColumn::make('declare_dt')
                    ->label('Start Date'),
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
