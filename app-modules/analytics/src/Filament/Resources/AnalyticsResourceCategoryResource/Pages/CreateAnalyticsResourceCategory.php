<?php

namespace AdvisingApp\Analytics\Filament\Resources\AnalyticsResourceCategoryResource\Pages;

use Filament\Forms\Form;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\CreateRecord;
use AdvisingApp\Analytics\Enums\AnalyticsResourceCategoryClassification;
use AdvisingApp\Analytics\Filament\Resources\AnalyticsResourceCategoryResource;

class CreateAnalyticsResourceCategory extends CreateRecord
{
    protected static string $resource = AnalyticsResourceCategoryResource::class;

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
                        Select::make('classification')
                            ->options(AnalyticsResourceCategoryClassification::class)
                            ->enum(AnalyticsResourceCategoryClassification::class)
                            ->required(),
                        Textarea::make('description')
                            ->nullable()
                            ->string(),
                    ]),
            ]);
    }
}
