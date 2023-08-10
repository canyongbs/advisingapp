<?php

namespace Assist\KnowledgeBase\Filament\Resources\KnowledgeBaseCategoryResource\Pages;

use Filament\Forms\Form;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\CreateRecord;
use Assist\KnowledgeBase\Filament\Resources\KnowledgeBaseCategoryResource;

class CreateKnowledgeBaseCategory extends CreateRecord
{
    protected static string $resource = KnowledgeBaseCategoryResource::class;

    public function form(Form $form): Form
    {
        return parent::form($form)
            ->schema([
                TextInput::make('name')
                    ->label('Name')
                    ->required()
                    ->string(),
            ]);
    }
}
