<?php

namespace Assist\Case\Filament\Resources\CaseItemTypeResource\Pages;

use Filament\Forms\Form;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\CreateRecord;
use Assist\Case\Filament\Resources\CaseItemTypeResource;

class CreateCaseItemType extends CreateRecord
{
    protected static string $resource = CaseItemTypeResource::class;

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
