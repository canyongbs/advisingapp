<?php

namespace AdvisingApp\StudentDataModel\Filament\Resources\StudentTagResource\Pages;

use Filament\Forms\Form;
use Filament\Actions\DeleteAction;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\EditRecord;
use AdvisingApp\StudentDataModel\Filament\Resources\StudentTagResource;

class EditStudentTag extends EditRecord
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

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
