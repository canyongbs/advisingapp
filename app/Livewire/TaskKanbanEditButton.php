<?php

namespace App\Livewire;

use Livewire\Component;
use Assist\Task\Models\Task;
use Filament\Actions\EditAction;
use Assist\Prospect\Models\Prospect;
use Filament\Forms\Components\Select;
use Filament\Forms\Contracts\HasForms;
use Illuminate\Database\Eloquent\Model;
use Filament\Forms\Components\TextInput;
use Assist\AssistDataModel\Models\Student;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\MorphToSelect;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Components\MorphToSelect\Type;
use Filament\Actions\Concerns\InteractsWithActions;

class TaskKanbanEditButton extends Component implements HasForms, HasActions
{
    use InteractsWithForms;
    use InteractsWithActions;

    public Task $task;

    public function render()
    {
        return view('livewire.task-kanban-edit-button');
    }

    public function mount(Task $task)
    {
        $this->task = $task;
    }

    public function editAction()
    {
        return EditAction::make('edit')
            ->record($this->task)
            ->form([
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
                    ->searchable(['name', 'email'])
                    ->default(auth()->id()),
                MorphToSelect::make('concern')
                    ->label('Concern')
                    ->searchable()
                    ->preload()
                    ->types([
                        Type::make(Student::class)
                            ->titleAttribute(Student::displayNameKey()),
                        Type::make(Prospect::class)
                            ->titleAttribute(Prospect::displayNameKey()),
                    ]),
            ])
            ->using(function (Model $record, array $data): Model {
                $data = collect($data);

                /** @var Task $record */
                $record->fill($data->except('assigned_to')->toArray());

                $record->assigned_to = $data->get('assigned_to');

                $record->save();

                return $record;
            });
    }
}
