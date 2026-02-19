<?php

/*
<COPYRIGHT>

    Copyright © 2016-2026, Canyon GBS LLC. All rights reserved.

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

namespace AdvisingApp\Campaign\Filament\Blocks;

use AdvisingApp\Campaign\Filament\Forms\Components\CampaignDateTimeInput;
use AdvisingApp\Task\Models\Task;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Utilities\Get;
use Illuminate\Support\Facades\Auth;

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
        return [
            Fieldset::make('Details')
                ->schema([
                    Fieldset::make($fieldPrefix . 'Confidentiality')
                        ->schema([
                            Checkbox::make('is_confidential')
                                ->label('Confidential')
                                ->live(),
                            Select::make('confidential_task_projects')
                                ->relationship('confidentialAccessProjects', 'name')
                                ->preload()
                                ->label('Projects')
                                ->multiple()
                                ->exists('projects', 'id')
                                ->visible(fn (Get $get) => $get('is_confidential')),
                            Select::make('confidential_task_users')
                                ->relationship('confidentialAccessUsers', 'name')
                                ->preload()
                                ->label('Users')
                                ->multiple()
                                ->exists('users', 'id')
                                ->visible(fn (Get $get) => $get('is_confidential')),
                            Select::make('confidential_task_teams')
                                ->relationship('confidentialAccessTeams', 'name')
                                ->preload()
                                ->label('Teams')
                                ->multiple()
                                ->exists('teams', 'id')
                                ->visible(fn (Get $get) => $get('is_confidential')),
                        ]),
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
                        ->model(Task::class)
                        ->nullable()
                        ->searchable()
                        ->default(Auth::id()),
                ]),
            CampaignDateTimeInput::make(),
        ];
    }

    public static function type(): string
    {
        return 'task';
    }
}
