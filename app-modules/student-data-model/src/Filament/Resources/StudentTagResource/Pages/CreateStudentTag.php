<?php

namespace AdvisingApp\StudentDataModel\Filament\Resources\StudentTagResource\Pages;

use Filament\Forms\Form;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\CreateRecord;
use AdvisingApp\StudentDataModel\Filament\Resources\StudentTagResource;

class CreateStudentTag extends CreateRecord
{
    protected static string $resource = StudentTagResource::class;

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->label('Name')
                    ->required()
                    ->maxLength(255)
                    ->string(),
            ]);
    }
}
