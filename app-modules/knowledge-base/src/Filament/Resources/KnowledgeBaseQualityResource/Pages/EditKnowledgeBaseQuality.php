<?php

namespace Assist\KnowledgeBase\Filament\Resources\KnowledgeBaseQualityResource\Pages;

use Filament\Actions;
use Filament\Forms\Form;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\EditRecord;
use Assist\KnowledgeBase\Filament\Resources\KnowledgeBaseQualityResource;

class EditKnowledgeBaseQuality extends EditRecord
{
    protected static string $resource = KnowledgeBaseQualityResource::class;

    public function form(Form $form): Form
    {
        return $form
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
