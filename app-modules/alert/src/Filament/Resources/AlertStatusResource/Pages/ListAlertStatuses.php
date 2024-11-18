<?php

namespace AdvisingApp\Alert\Filament\Resources\AlertStatusResource\Pages;

use AdvisingApp\Alert\Filament\Resources\AlertStatusResource;
use AdvisingApp\Alert\Models\AlertStatus;
use App\Filament\Tables\Columns\IdColumn;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ListAlertStatuses extends ListRecords
{
    protected static string $resource = AlertStatusResource::class;

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                IdColumn::make(),
                TextColumn::make('name')
                    ->label('Name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('classification')
                    ->label('Classification')
                    ->searchable()
                    ->sortable(),
                IconColumn::make('is_default')
                    ->label('Set as default')
                    ->state(fn(AlertStatus $alertStatus): bool => $alertStatus->is_default)
                    ->boolean(),
                TextColumn::make('alerts_count')
                    ->label('# of Alerts')
                    ->counts('alerts')
                    ->sortable(),
            ])
            ->defaultSort('sort')
            ->reorderable('sort')
            ->actions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make()
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
