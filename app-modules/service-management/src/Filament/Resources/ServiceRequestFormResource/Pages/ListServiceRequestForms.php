<?php

namespace AdvisingApp\ServiceManagement\Filament\Resources\ServiceRequestFormResource\Pages;

use Filament\Tables\Table;
use App\Filament\Columns\IdColumn;
use Filament\Actions\CreateAction;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use AdvisingApp\ServiceManagement\Models\ServiceRequestForm;
use AdvisingApp\ServiceManagement\Filament\Resources\ServiceRequestFormResource;

class ListServiceRequestForms extends ListRecords
{
    protected static string $resource = ServiceRequestFormResource::class;

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                IdColumn::make(),
                TextColumn::make('name'),
            ])
            ->actions([
                Action::make('Respond')
                    ->url(fn (ServiceRequestForm $form) => route('service-request-form.show', ['service-request-form' => $form]))
                    ->icon('heroicon-m-arrow-top-right-on-square')
                    ->openUrlInNewTab()
                    ->color('gray'),
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
