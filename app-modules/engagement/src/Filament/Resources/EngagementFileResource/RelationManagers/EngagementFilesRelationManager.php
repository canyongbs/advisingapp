<?php

namespace Assist\Engagement\Filament\Resources\EngagementFileResource\RelationManagers;

use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Filament\Columns\IdColumn;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use App\Filament\Resources\RelationManagers\RelationManager;

class EngagementFilesRelationManager extends RelationManager
{
    protected static string $relationship = 'engagementFiles';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('description')
                    ->required()
                    ->maxLength(255),
                DatePicker::make('retention_date')
                    ->label('Retention Date')
                    ->native(false)
                    ->closeOnDateSelection()
                    ->format('Y-m-d')
                    ->displayFormat('Y-m-d')
                    ->minDate(now()->addDay())
                    ->required(),
                SpatieMediaLibraryFileUpload::make('file')
                    ->label('File')
                    ->disk('s3')
                    ->collection('file')
                    ->required(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('description')
            ->columns([
                IdColumn::make(),
                TextColumn::make('description'),
                SpatieMediaLibraryImageColumn::make('file')
                    ->collection('file')
                    ->visibility('private'),
            ])
            ->filters([
            ])
            ->headerActions([
                CreateAction::make(),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
