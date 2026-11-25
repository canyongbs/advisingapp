<?php

namespace AdvisingApp\Organization\Filament\Tables;

use AdvisingApp\Organization\Models\Organization;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class SelectableOrganizationsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->query(fn (): Builder => Organization::query())
            ->columns([
                TextColumn::make('name')
                    ->label('Name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('domains')
                    ->label('Domains')
                    ->state(fn (Organization $record): array => collect($record->domains)->pluck('domain')->all())
                    ->listWithLineBreaks(),
            ])
            ->defaultSort('name');
    }
}
