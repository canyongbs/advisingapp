<?php

namespace Assist\Engagement\Filament\Resources;

use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Assist\Engagement\Models\EngagementFile;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Assist\Engagement\Filament\Resources\EngagementFileResource\Pages;

class EngagementFileResource extends Resource
{
    protected static ?string $model = EngagementFile::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-duplicate';

    protected static ?string $navigationGroup = 'Productivity Tools';

    protected static ?int $navigationSort = 6;

    protected static ?string $navigationLabel = 'Files and Documents';

    protected ?string $heading = 'Files and Documents';

    protected static ?string $modelLabel = 'File or Document';

    protected static ?string $pluralModelLabel = 'Files or Documents';

    public static function getRelations(): array
    {
        return [
        ];
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('description')
                    ->required()
                    ->maxLength(255),
                DatePicker::make('retention_date')
                    ->label('Retention Date')
                    ->hintIcon('heroicon-m-question-mark-circle', tooltip: 'The file will be deleted automatically after this date. If left blank, the file will be kept indefinitely.')
                    ->native(false)
                    ->closeOnDateSelection()
                    ->format('Y-m-d')
                    ->minDate(now()->addDay()),
                SpatieMediaLibraryFileUpload::make('file')
                    ->label('File')
                    ->disk('s3')
                    ->collection('file')
                    ->required(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEngagementFiles::route('/'),
            'create' => Pages\CreateEngagementFile::route('/create'),
            'view' => Pages\ViewEngagementFile::route('/{record}'),
            'edit' => Pages\EditEngagementFile::route('/{record}/edit'),
        ];
    }
}
