<?php

namespace Assist\AssistDataModel\Filament\Resources\StudentResource\RelationManagers;

use Filament\Tables\Table;
use Filament\Infolists\Infolist;
use App\Filament\Columns\IdColumn;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Infolists\Components\TextEntry;
use App\Filament\Resources\RelationManagers\RelationManager;

class EnrollmentsRelationManager extends RelationManager
{
    protected static string $relationship = 'enrollments';

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema(
                [
                    TextEntry::make('sisid')
                        ->label('SISID'),
                    TextEntry::make('division')
                        ->label('College'),
                    TextEntry::make('class_nbr')
                        ->label('Course'),
                    TextEntry::make('crse_grade_off')
                        ->label('Grade'),
                    TextEntry::make('unt_taken')
                        ->label('Attempted'),
                    TextEntry::make('unt_earned')
                        ->label('Earned'),
                ]
            );
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('division')
            ->columns([
                IdColumn::make(),
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
            ->bulkActions([]);
    }
}
