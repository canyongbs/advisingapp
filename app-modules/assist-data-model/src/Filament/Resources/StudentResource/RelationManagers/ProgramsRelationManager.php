<?php

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
            ->bulkActions([])
            ->emptyStateActions([]);
    }
}
