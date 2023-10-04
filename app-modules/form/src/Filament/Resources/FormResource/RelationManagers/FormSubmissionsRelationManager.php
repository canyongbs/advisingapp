<?php

namespace Assist\Form\Filament\Resources\FormResource\RelationManagers;

use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Forms\Components\KeyValue;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use App\Filament\Resources\RelationManagers\RelationManager;

class FormSubmissionsRelationManager extends RelationManager
{
    protected static string $relationship = 'submissions';

    public function form(Form $form): Form
    {
        return parent::form($form)
            ->schema([
                KeyValue::make('content')
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->columns([
                TextColumn::make('id'),
                TextColumn::make('created_at')
                    ->sortable(),
            ])
            ->filters([
            ])
            ->headerActions([
            ])
            ->actions([
                ViewAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateActions([
            ]);
    }
}
