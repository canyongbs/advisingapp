<?php

namespace AdvisingApp\Form\Filament\Resources\FormResource\Pages;

use AdvisingApp\Form\Filament\Resources\FormResource;
use Filament\Actions;
use Filament\Forms;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Pages\ManageRelatedRecords;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ManageFormWorkflows extends ManageRelatedRecords
{
    protected static string $resource = FormResource::class;

    protected static string $relationship = 'workflows'; //TODO: make this relationship

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function getNavigationLabel(): string
    {
        return 'Workflows';
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Toggle::make('is_enabled')
                    ->label('Enabled?'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name') //is this necessary?
            ->columns([
                TextColumn::make('name'),
                TextColumn::make('is_enabled')
                    ->label('Enabled')
                    ->icon(fn ($record): string => $record->is_enabled ? 'heroicon-m-check-circle' : 'heroicon-m-x-circle'),
            ])
            ->filters([
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
            ])
            ->modifyQueryUsing(fn (Builder $query) => $query->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]));
    }
}
