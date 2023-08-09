<?php

namespace Assist\Prospect\Filament\Resources\ProspectSourceResource\Pages;

use Filament\Forms\Form;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\CreateRecord;
use Assist\Prospect\Filament\Resources\ProspectSourceResource;

class CreateProspectSource extends CreateRecord
{
    protected static string $resource = ProspectSourceResource::class;

    public function form(Form $form): Form
    {
        return parent::form($form)
            ->schema([
                TextInput::make('name')
                    ->label('Name')
                    ->translateLabel()
                    ->required()
                    ->string(),
            ]);
    }
}
