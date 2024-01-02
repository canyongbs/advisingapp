<?php

namespace AdvisingApp\Analytics\Filament\Resources\AnalyticsResourceSourceResource\Pages;

use AdvisingApp\Analytics\Filament\Resources\AnalyticsResourceSourceResource;
use App\Filament\Columns\IdColumn;
use Filament\Actions;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ListAnalyticsResourceSources extends ListRecords
{
    protected static string $resource = AnalyticsResourceSourceResource::class;

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                IdColumn::make(),
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('resources_count')
                    ->label('# of Analytics Resources')
                    ->counts('resources')
                    ->sortable(),
            ])
            ->filters([
            ])
            ->actions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
