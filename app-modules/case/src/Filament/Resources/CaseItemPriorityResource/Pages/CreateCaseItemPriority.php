<?php

namespace Assist\Case\Filament\Resources\CaseItemPriorityResource\Pages;

use Filament\Forms\Form;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\CreateRecord;
use Assist\Case\Filament\Resources\CaseItemPriorityResource;

class CreateCaseItemPriority extends CreateRecord
{
    protected static string $resource = CaseItemPriorityResource::class;

    public function form(Form $form): Form
    {
        return parent::form($form)
            ->schema([
                TextInput::make('name')
                    ->label('Name')
                    ->required()
                    ->string(),
                TextInput::make('order')
                    ->label('Priority Order')
                    ->required()
                    ->integer()
                    ->numeric(),
            ]);
    }
}
