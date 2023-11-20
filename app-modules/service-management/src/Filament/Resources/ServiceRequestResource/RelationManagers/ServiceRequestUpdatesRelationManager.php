<?php

namespace Assist\ServiceManagement\Filament\Resources\ServiceRequestResource\RelationManagers;

use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Filament\Columns\IdColumn;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Assist\ServiceManagement\Models\ServiceRequestUpdate;
use App\Filament\Resources\RelationManagers\RelationManager;
use Assist\ServiceManagement\Enums\ServiceRequestUpdateDirection;
use Assist\ServiceManagement\Filament\Resources\ServiceRequestUpdateResource;

class ServiceRequestUpdatesRelationManager extends RelationManager
{
    protected static string $relationship = 'serviceRequestUpdates';

    protected static ?string $recordTitleAttribute = 'update';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Textarea::make('update')
                    ->label('Update')
                    ->rows(3)
                    ->columnSpan('full')
                    ->required()
                    ->string(),
                Select::make('direction')
                    ->options(ServiceRequestUpdateDirection::class)
                    ->label('Direction')
                    ->required()
                    ->enum(ServiceRequestUpdateDirection::class)
                    ->default(ServiceRequestUpdateDirection::default()),
                Toggle::make('internal')
                    ->label('Internal')
                    ->rule(['boolean']),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                IdColumn::make(),
                TextColumn::make('update')
                    ->label('Update')
                    ->translateLabel()
                    ->words(6),
                IconColumn::make('internal')
                    ->boolean(),
                TextColumn::make('direction')
                    ->icon(fn (ServiceRequestUpdateDirection $state): string => $state->getIcon())
                    ->formatStateUsing(fn (ServiceRequestUpdateDirection $state): string => $state->getLabel()),
                TextColumn::make('created_at')
                    ->sortable(),
                TextColumn::make('updated_at')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->url(fn (ServiceRequestUpdate $serviceRequestUpdate) => ServiceRequestUpdateResource::getUrl('view', ['record' => $serviceRequestUpdate])),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
