<?php

namespace AdvisingApp\Analytics\Filament\Resources\AnalyticsResourceResource\Pages;

use Filament\Forms\Form;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\CreateRecord;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use AdvisingApp\Analytics\Filament\Resources\AnalyticsResourceResource;

class CreateAnalyticsResource extends CreateRecord
{
    protected static string $resource = AnalyticsResourceResource::class;

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()
                    ->columns()
                    ->schema([
                        TextInput::make('name')
                            ->required()
                            ->string()
                            ->unique(ignoreRecord: true),
                        Textarea::make('description')
                            ->nullable()
                            ->string(),
                        Select::make('category_id')
                            ->relationship('category', 'name')
                            ->required(),
                        Select::make('source_id')
                            ->relationship('source', 'name'),
                        TextInput::make('url')
                            ->nullable()
                            ->url(),
                        SpatieMediaLibraryFileUpload::make('thumbnail')
                            ->nullable(),
                        Checkbox::make('is_active')
                            ->label('Active'),
                        Checkbox::make('is_included_in_data_portal')
                            ->label('Included in Data Portal'),
                    ]),
            ]);
    }
}
