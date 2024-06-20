<?php

namespace AdvisingApp\BasicNeeds\Filament\Resources\BasicNeedCategoryResource\Pages;

use Filament\Actions;
use Filament\Forms\Form;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\EditRecord;
use AdvisingApp\BasicNeeds\Filament\Resources\BasicNeedCategoryResource;

class EditBasicNeedCategory extends EditRecord
{
    protected static string $resource = BasicNeedCategoryResource::class;

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->label('Category Name')
                    ->required()
                    ->maxLength(255)
                    ->string(),
                Textarea::make('description')
                    ->label('Description')
                    ->maxLength(65535)
                    ->string(),
            ])->columns(1);
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
