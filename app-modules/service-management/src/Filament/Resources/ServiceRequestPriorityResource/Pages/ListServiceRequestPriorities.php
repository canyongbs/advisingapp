<?php

namespace Assist\ServiceManagement\Filament\Resources\ServiceRequestPriorityResource\Pages;

use Filament\Actions;
use Filament\Tables\Table;
use App\Filament\Columns\IdColumn;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Assist\ServiceManagement\Filament\Resources\ServiceRequestPriorityResource;

class ListServiceRequestPriorities extends ListRecords
{
    protected static string $resource = ServiceRequestPriorityResource::class;

    public function table(Table $table): Table
    {
        return parent::table($table)
            ->columns([
                IdColumn::make(),
                TextColumn::make('name')
                    ->label('Name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('order')
                    ->label('Priority Order')
                    ->sortable(),
                TextColumn::make('service_requests_count')
                    ->label('# of Service Requests')
                    ->counts('serviceRequests')
                    ->sortable(),
            ])
            ->defaultSort('order')
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
            ->reorderable('order');
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
