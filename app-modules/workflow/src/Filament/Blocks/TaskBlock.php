<?php

/*
<COPYRIGHT>

    Copyright © 2016-2025, Canyon GBS LLC. All rights reserved.

    Advising App™ is licensed under the Elastic License 2.0. For more details,
    see https://github.com/canyongbs/advisingapp/blob/main/LICENSE.

    Notice:

    - You may not provide the software to third parties as a hosted or managed
      service, where the service provides users with access to any substantial set of
      the features or functionality of the software.
    - You may not move, change, disable, or circumvent the license key functionality
      in the software, and you may not remove or obscure any functionality in the
      software that is protected by the license key.
    - You may not alter, remove, or obscure any licensing, copyright, or other notices
      of the licensor in the software. Any use of the licensor’s trademarks is subject
      to applicable law.
    - Canyon GBS LLC respects the intellectual property rights of others and expects the
      same in return. Canyon GBS™ and Advising App™ are registered trademarks of
      Canyon GBS LLC, and we are committed to enforcing and protecting our trademarks
      vigorously.
    - The software solution, including services, infrastructure, and code, is offered as a
      Software as a Service (SaaS) by Canyon GBS LLC.
    - Use of this software implies agreement to the license terms and conditions as stated
      in the Elastic License 2.0.

    For more information or inquiries please visit our website at
    https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

namespace AdvisingApp\Workflow\Filament\Blocks;

use AdvisingApp\Workflow\Models\WorkflowTaskDetails;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Illuminate\Support\Facades\Auth;

class TaskBlock extends WorkflowActionBlock
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->label('Task');

        $this->schema($this->createFields());
    }

    /**
     * @return array<int, Section|Fieldset|Checkbox|Select|Textarea|TextInput|DateTimePicker>
     */
    public function generateFields(string $fieldPrefix = ''): array
    {
        return [
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
                ->model(WorkflowTaskDetails::class)
                ->nullable()
                ->searchable()
                ->default(Auth::id()),
            Section::make('How long after the previous step should this occur?')
                ->schema([
                    TextInput::make('days')
                        ->translateLabel()
                        ->numeric()
                        ->step(1)
                        ->minValue(0)
                        ->default(0)
                        ->inlineLabel(),
                    TextInput::make('hours')
                        ->translateLabel()
                        ->numeric()
                        ->step(1)
                        ->minValue(0)
                        ->default(0)
                        ->inlineLabel(),
                    TextInput::make('minutes')
                        ->translateLabel()
                        ->numeric()
                        ->step(1)
                        ->minValue(0)
                        ->default(0)
                        ->inlineLabel(),
                ])
                ->columns(3),
        ];
    }

    public static function type(): string
    {
        return 'workflow_task_details';
    }
}
