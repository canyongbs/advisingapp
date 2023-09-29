<?php

namespace Assist\Form\Filament\Resources\FormResource\Pages;

use Filament\Forms\Form;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\CreateRecord;
use Assist\Form\Filament\Resources\FormResource;

class CreateForm extends CreateRecord
{
    protected static string $resource = FormResource::class;

    public function form(Form $form): Form
    {
        return parent::form($form)
            ->schema([
                TextInput::make('name')
                    ->required()
                    ->string()
                    ->maxLength(255)
                    ->autocomplete(false)
                    ->columnSpanFull(),
                Textarea::make('description')
                    ->string()
                    ->columnSpanFull(),
            ]);
    }
}
