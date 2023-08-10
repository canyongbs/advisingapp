<?php

namespace Assist\KnowledgeBase\Filament\Resources\KnowledgeBaseItemResource\Pages;

use Filament\Forms\Form;
use FilamentTiptapEditor\TiptapEditor;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\CreateRecord;
use Assist\KnowledgeBase\Filament\Resources\KnowledgeBaseItemResource;

class CreateKnowledgeBaseItem extends CreateRecord
{
    protected static string $resource = KnowledgeBaseItemResource::class;

    public function form(Form $form): Form
    {
        return parent::form($form)
            ->schema([
                TextInput::make('question')
                    ->label('Question/Issue/Feature')
                    ->required()
                    ->string(),
                TiptapEditor::make('solution')
                    ->label('Solution')
                    ->columnSpanFull()
                    ->extraInputAttributes(['style' => 'min-height: 12rem;'])
                    ->required()
                    ->string(),
            ]);
    }
}
