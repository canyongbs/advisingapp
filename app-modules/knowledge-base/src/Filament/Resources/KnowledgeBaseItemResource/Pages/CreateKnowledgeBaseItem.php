<?php

namespace Assist\KnowledgeBase\Filament\Resources\KnowledgeBaseItemResource\Pages;

use Filament\Forms\Form;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\RichEditor;
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
                RichEditor::make('solution')
                    ->label('Solution')
                    ->required()
                    ->string(),
            ]);
    }
}
