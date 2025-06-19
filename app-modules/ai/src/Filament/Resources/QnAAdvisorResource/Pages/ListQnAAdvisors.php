<?php

namespace AdvisingApp\Ai\Filament\Resources\QnAAdvisorResource\Pages;

use AdvisingApp\Ai\Filament\Resources\QnAAdvisorResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ListQnAAdvisors extends ListRecords
{
    protected static string $resource = QnAAdvisorResource::class;

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                SpatieMediaLibraryImageColumn::make('avatar')
                    ->collection('avatar')
                    ->visibility('private')
                    ->circular(),
                TextColumn::make('name')
                    ->searchable()
                    ->label('Name'),
                IconColumn::make('archived_at')
                    ->label('Archived')
                    ->boolean()
                    ->getStateUsing(fn ($record) => $record->archived_at !== null ? true : false)
                    ->hidden(function (Table $table) {
                        return $table->getFilter('withoutArchived')->getState()['isActive'] ?? false;
                    }),
            ])
            ->filters([
                Filter::make('withoutArchived')
                    ->query(fn (Builder $query) => $query->whereNull('archived_at'))
                    ->default(),
            ])
            ->actions([
                ViewAction::make(),
                EditAction::make(),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
