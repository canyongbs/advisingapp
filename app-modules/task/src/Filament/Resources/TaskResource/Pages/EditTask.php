<?php

namespace Assist\Task\Filament\Resources\TaskResource\Pages;

use Filament\Actions;
use Filament\Forms\Form;
use Assist\Task\Models\Task;
use Assist\Prospect\Models\Prospect;
use Filament\Forms\Components\Select;
use Illuminate\Database\Eloquent\Model;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\EditRecord;
use Assist\AssistDataModel\Models\Student;
use Filament\Forms\Components\MorphToSelect;
use Filament\Forms\Components\DateTimePicker;
use Assist\Task\Filament\Resources\TaskResource;
use Filament\Forms\Components\MorphToSelect\Type;

class EditTask extends EditRecord
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

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $data = collect($data);

        /** @var Task $record */
        $record->fill($data->except('assigned_to')->toArray());

        $record->assigned_to = $data->get('assigned_to');

        $record->save();

        return $record;
    }
}
