<?php

namespace Assist\Task\Filament\Resources\TaskResource\Pages;

use Filament\Forms\Form;
use Assist\Prospect\Models\Prospect;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Assist\AssistDataModel\Models\Student;
use Filament\Resources\Pages\CreateRecord;
use Filament\Forms\Components\MorphToSelect;
use Filament\Forms\Components\DateTimePicker;
use Assist\Task\Filament\Resources\TaskResource;
use Filament\Forms\Components\MorphToSelect\Type;

class CreateTask extends CreateRecord
{
    protected static string $resource = TaskResource::class;

    public function form(Form $form): Form
    {
        return parent::form($form)
            ->schema([
                TextInput::make('description')
                    ->label('Description')
                    ->required()
                    ->string(),
                DateTimePicker::make('due')
                    ->label('Due Date')
                    ->native(false),
                Select::make('assigned_to')
                    ->label('Assigned To')
                    ->relationship('assignedTo', 'name')
                    ->nullable()
                    ->searchable()
                    ->default(auth()->id()),
                MorphToSelect::make('concern')
                    ->label('Concern')
                    ->searchable()
                    ->preload()
                    ->types([
                        Type::make(Student::class)
                            ->titleAttribute('full'),
                        Type::make(Prospect::class)
                            ->titleAttribute('full'),
                    ]),
            ]);
    }
}
