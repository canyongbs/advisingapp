<?php

namespace AdvisingApp\Assistant\Filament\Resources\PromptResource\Pages;

use Filament\Forms\Form;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\CreateRecord;
use AdvisingApp\Assistant\Filament\Resources\PromptResource;

class CreatePrompt extends CreateRecord
{
    protected static string $resource = PromptResource::class;

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()
                    ->columns()
                    ->schema([
                        TextInput::make('title')
                            ->unique()
                            ->required()
                            ->string()
                            ->maxLength(255),
                        Select::make('type_id')
                            ->relationship('type', 'title')
                            ->preload()
                            ->searchable()
                            ->required(),
                        Textarea::make('description')
                            ->string()
                            ->columnSpanFull(),
                        Textarea::make('prompt')
                            ->required()
                            ->string()
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
