<?php

namespace Assist\AssistDataModel\Filament\Resources\StudentResource\RelationManagers;

use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Actions\ViewAction;
use App\Filament\Resources\RelationManagers\RelationManager;

class EnrollmentsRelationManager extends RelationManager
{
    protected static string $relationship = 'enrollments';

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('division')
            ->columns([
                TextColumn::make('division')
                    ->label('College'),
                TextColumn::make('class_nbr')
                    ->label('Course'),
                TextColumn::make('crse_grade_off')
                    ->label('Grade'),
                TextColumn::make('unt_taken')
                    ->label('Attempted'),
                TextColumn::make('unt_earned')
                    ->label('Earned'),
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
