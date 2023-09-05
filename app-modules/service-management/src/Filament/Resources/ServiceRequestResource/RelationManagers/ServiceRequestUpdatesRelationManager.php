<?php

namespace Assist\ServiceManagement\Filament\Resources\ServiceRequestResource\RelationManagers;

use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Resources\RelationManagers\RelationManager;
use Assist\ServiceManagement\Models\ServiceRequestUpdate;
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
                    ->options(collect(ServiceRequestUpdateDirection::cases())->mapWithKeys(fn (ServiceRequestUpdateDirection $direction) => [$direction->value => $direction->name]))
                    ->label('Direction')
                    ->required()
                    ->enum(ServiceRequestUpdateDirection::class),
                Toggle::make('internal')
                    ->label('Internal')
                    ->rule(['boolean']),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('update')
                    ->label('Update')
                    ->translateLabel()
                    ->words(6),
                IconColumn::make('internal')
                    ->boolean(),
                TextColumn::make('direction')
                    ->icon(fn (ServiceRequestUpdateDirection $state): string => match ($state) {
                        ServiceRequestUpdateDirection::Inbound => 'heroicon-o-arrow-down-tray',
                        ServiceRequestUpdateDirection::Outbound => 'heroicon-o-arrow-up-tray',
                    })
                    ->formatStateUsing(fn (ServiceRequestUpdateDirection $state): string => Str::ucfirst($state->value)),
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
            ])
            ->emptyStateActions([
                Tables\Actions\CreateAction::make(),
            ]);
    }
}
