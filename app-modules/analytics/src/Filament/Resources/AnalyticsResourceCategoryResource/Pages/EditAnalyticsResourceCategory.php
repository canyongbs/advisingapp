<?php

namespace AdvisingApp\Analytics\Filament\Resources\AnalyticsResourceCategoryResource\Pages;

use Filament\Forms\Form;
use Filament\Actions\ViewAction;
use Filament\Actions\DeleteAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\EditRecord;
use AdvisingApp\Analytics\Enums\AnalyticsResourceCategoryClassification;
use AdvisingApp\Analytics\Filament\Resources\AnalyticsResourceCategoryResource;

class EditAnalyticsResourceCategory extends EditRecord
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

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
