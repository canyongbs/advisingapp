<?php

namespace AdvisingApp\Assistant\Filament\Resources\PromptTypeResource\Pages;

use Filament\Forms\Form;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\CreateRecord;
use AdvisingApp\Assistant\Filament\Resources\PromptTypeResource;

class CreatePromptType extends CreateRecord
{
    protected static string $resource = PromptTypeResource::class;

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
                            ->maxLength(255)
                            ->columnSpanFull(),
                        Textarea::make('description')
                            ->string()
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
