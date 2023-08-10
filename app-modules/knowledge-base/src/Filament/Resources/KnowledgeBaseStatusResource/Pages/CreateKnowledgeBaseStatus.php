<?php

namespace Assist\KnowledgeBase\Filament\Resources\KnowledgeBaseStatusResource\Pages;

use Filament\Forms\Form;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\CreateRecord;
use Assist\KnowledgeBase\Filament\Resources\KnowledgeBaseStatusResource;

class CreateKnowledgeBaseStatus extends CreateRecord
{
    protected static string $resource = KnowledgeBaseStatusResource::class;

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
