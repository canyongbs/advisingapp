<?php

namespace AdvisingApp\BasicNeeds\Filament\Resources\BasicNeedCategoryResource\Pages;

use Filament\Forms\Form;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\CreateRecord;
use AdvisingApp\BasicNeeds\Filament\Resources\BasicNeedCategoryResource;

class CreateBasicNeedCategory extends CreateRecord
{
    protected static string $resource = BasicNeedCategoryResource::class;

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->label('Category Name')
                    ->required()
                    ->string()
                    ->maxLength(255)
                    ->unique(),
                Textarea::make('description')
                    ->label('Description')
                    ->maxLength(65535)
                    ->string(),
            ])->columns(1);
    }
}
