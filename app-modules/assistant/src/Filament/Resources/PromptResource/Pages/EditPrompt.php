<?php

namespace AdvisingApp\Assistant\Filament\Resources\PromptResource\Pages;

use Filament\Forms\Form;
use Filament\Actions\ViewAction;
use Filament\Actions\DeleteAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\EditRecord;
use AdvisingApp\Assistant\Filament\Resources\PromptResource;

class EditPrompt extends EditRecord
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
                            ->unique(ignoreRecord: true)
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

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
