<?php

namespace Assist\KnowledgeBase\Filament\Resources\KnowledgeBaseQualityResource\Pages;

use Filament\Forms\Form;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\CreateRecord;
use Assist\KnowledgeBase\Filament\Resources\KnowledgeBaseQualityResource;

class CreateKnowledgeBaseQuality extends CreateRecord
{
    protected static string $resource = KnowledgeBaseQualityResource::class;

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
