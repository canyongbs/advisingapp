<?php

namespace AdvisingApp\BasicNeeds\Filament\Resources\BasicNeedsProgramResource\RelationManagers;

use Filament\Tables\Table;
use Filament\Tables\Actions\AttachAction;
use Filament\Tables\Actions\DetachAction;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DetachBulkAction;
use App\Filament\Tables\Columns\OpenSearch\TextColumn;
use Filament\Resources\RelationManagers\RelationManager;

class ProspectsRelationManager extends RelationManager
{
    protected static string $relationship = 'prospects';

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('full_name')
            ->columns([
                TextColumn::make('full_name')
                    ->label('Name')
                    ->searchable(),
                TextColumn::make('email')
                    ->label('Email')
                    ->searchable(),
                TextColumn::make('mobile')
                    ->label('Mobile')
                    ->searchable(),
            ])
            ->filters([
            ])
            ->headerActions([
                AttachAction::make(),
                // ->recordSelectOptionsQuery(fn (Builder $query) => $query->join('program_participants', 'basic_needs_programs.id', '=', 'program_participants.basic_needs_program_id')),
            ])
            ->actions([
                DetachAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DetachBulkAction::make(),
                ]),
            ]);
    }
}
