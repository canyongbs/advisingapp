<?php

namespace Assist\Engagement\Filament\Resources\EngagementFileResource\RelationManagers;

use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Filament\Columns\IdColumn;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use App\Filament\Resources\RelationManagers\RelationManager;
use Assist\Engagement\Filament\Resources\EngagementFileResource;

class EngagementFilesRelationManager extends RelationManager
{
    protected static string $relationship = 'engagementFiles';

    protected static ?string $label = 'Files and Documents';

    protected static ?string $title = 'Files and Documents';

    protected static ?string $modelLabel = 'File';

    public function form(Form $form): Form
    {
        return EngagementFileResource::form($form);
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
