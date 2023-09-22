<?php

namespace Assist\KnowledgeBase\Filament\Resources\KnowledgeBaseStatusResource\Pages;

use Filament\Actions;
use Filament\Forms\Form;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\EditRecord;
use Assist\KnowledgeBase\Filament\Resources\KnowledgeBaseStatusResource;

class EditKnowledgeBaseStatus extends EditRecord
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

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
