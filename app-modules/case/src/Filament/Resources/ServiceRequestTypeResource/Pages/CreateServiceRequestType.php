<?php

namespace Assist\Case\Filament\Resources\ServiceRequestTypeResource\Pages;

use Filament\Forms\Form;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\CreateRecord;
use Assist\Case\Filament\Resources\ServiceRequestTypeResource;

class CreateServiceRequestType extends CreateRecord
{
    protected static string $resource = ServiceRequestTypeResource::class;

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
