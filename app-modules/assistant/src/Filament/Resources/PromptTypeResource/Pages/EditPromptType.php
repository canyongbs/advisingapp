<?php

namespace AdvisingApp\Assistant\Filament\Resources\PromptTypeResource\Pages;

use Filament\Forms\Form;
use Filament\Actions\ViewAction;
use Filament\Actions\DeleteAction;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\EditRecord;
use AdvisingApp\Assistant\Filament\Resources\PromptTypeResource;

class EditPromptType extends EditRecord
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
                            ->unique(ignoreRecord: true)
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

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
