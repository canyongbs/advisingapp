<?php

namespace Assist\Division\Filament\Resources\DivisionResource\Pages;

use Filament\Tables\Table;
use App\Filament\Columns\IdColumn;
use Filament\Actions\CreateAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Assist\Division\Filament\Resources\DivisionResource;
use Filament\Tables\Actions\CreateAction as TableCreateAction;

class ListDivisions extends ListRecords
{
    protected static string $resource = DivisionResource::class;

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                IdColumn::make(),
                TextColumn::make('name')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('code')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('createdBy.name')
                    ->default('N/A')
                    ->label('Created By')
                    ->sortable(),
                TextColumn::make('updated_at')
                    ->label('Updated At')
                    ->dateTime(config('project.datetime_format') ?? 'Y-m-d H:i:s')
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
            ])
            ->emptyStateActions([
                TableCreateAction::make(),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
