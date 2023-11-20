<?php

namespace Assist\Campaign\Filament\Blocks;

use App\Models\User;
use Assist\Task\Models\Task;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DateTimePicker;

class TaskBlock extends CampaignActionBlock
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->model(Task::class);

        $this->schema($this->createFields());
    }

    public function generateFields(string $fieldPrefix = ''): array
    {
        /** @var User $user */
        $user = auth()->user();

        return [
            Fieldset::make('Details')
                ->schema([
                    TextInput::make($fieldPrefix . 'title')
                        ->required()
                        ->maxLength(100)
                        ->string(),
                    Textarea::make($fieldPrefix . 'description')
                        ->required()
                        ->string(),
                    DateTimePicker::make($fieldPrefix . 'due')
                        ->label('Due Date'),
                    Select::make($fieldPrefix . 'assigned_to')
                        ->label('Assigned To')
                        ->relationship('assignedTo', 'name')
                        ->nullable()
                        ->searchable()
                        ->default(auth()->id()),
                ]),
            DateTimePicker::make($fieldPrefix . 'execute_at')
                ->label('When should the journey step be executed?')
                ->required()
                ->minDate(now($user->timezone))
                ->closeOnDateSelection(),
        ];
    }

    public static function type(): string
    {
        return 'task';
    }
}
