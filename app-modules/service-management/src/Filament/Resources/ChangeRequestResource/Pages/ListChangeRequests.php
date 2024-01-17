<?php

namespace AdvisingApp\ServiceManagement\Filament\Resources\ChangeRequestResource\Pages;

use Filament\Tables\Table;
use App\Filament\Columns\IdColumn;
use Filament\Actions\CreateAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ViewColumn;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use AdvisingApp\ServiceManagement\Filament\Resources\ChangeRequestResource;

class ListChangeRequests extends ListRecords
{
    protected static string $resource = ChangeRequestResource::class;

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                IdColumn::make(),
                TextColumn::make('title')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('type.name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('status.name')
                    ->searchable()
                    ->sortable(),
                ViewColumn::make('risk_score')->view('filament.tables.columns.change-request.risk-score'),
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
