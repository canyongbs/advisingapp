<?php

namespace Assist\Task\Filament\Resources\TaskResource\Pages;

use Filament\Forms\Form;
use Filament\Facades\Filament;
use Assist\Prospect\Models\Prospect;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Illuminate\Database\Eloquent\Model;
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
                TextInput::make('title')
                    ->required()
                    ->maxLength(100)
                    ->string(),
                Textarea::make('description')
                    ->required()
                    ->string(),
                DateTimePicker::make('due')
                    ->label('Due Date')
                    ->native(false),
                Select::make('assigned_to')
                    ->label('Assigned To')
                    ->relationship('assignedTo', 'name')
                    ->nullable()
                    ->searchable(['name', 'email'])
                    ->default(auth()->id()),
                MorphToSelect::make('concern')
                    ->label('Related To')
                    ->searchable()
                    ->types([
                        Type::make(Student::class)
                            ->titleAttribute(Student::displayNameKey()),
                        Type::make(Prospect::class)
                            ->titleAttribute(Prospect::displayNameKey()),
                    ]),
            ]);
    }

    protected function handleRecordCreation(array $data): Model
    {
        $data = collect($data);

        $record = new ($this->getModel())($data->except('assigned_to')->toArray());

        $record->assigned_to = $data->get('assigned_to');

        if ($tenant = Filament::getTenant()) {
            return $this->associateRecordWithTenant($record, $tenant);
        }

        $record->save();

        return $record;
    }
}
