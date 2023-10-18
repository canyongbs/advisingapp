<?php

namespace Assist\ServiceManagement\Filament\Resources\ServiceRequestStatusResource\Pages;

use Filament\Actions;
use Filament\Tables\Table;
use App\Filament\Columns\IdColumn;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Assist\ServiceManagement\Models\ServiceRequestStatus;
use Assist\ServiceManagement\Filament\Resources\ServiceRequestStatusResource;

class ListServiceRequestStatuses extends ListRecords
{
    protected static string $resource = ServiceRequestStatusResource::class;

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
                    ->label('Classification'),
                TextColumn::make('color')
                    ->label('Color')
                    ->badge()
                    ->color(fn (ServiceRequestStatus $serviceRequestStatus) => $serviceRequestStatus->color->value),
                TextColumn::make('service_requests_count')
                    ->label('# of Service Requests')
                    ->counts('serviceRequests')
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
            Actions\CreateAction::make(),
        ];
    }
}
