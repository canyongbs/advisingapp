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

namespace AdvisingApp\Project\Filament\Resources\Projects\Pages;

use AdvisingApp\Project\Filament\Resources\Projects\ProjectResource;
use AdvisingApp\Prospect\Filament\Resources\Prospects\ProspectResource;
use AdvisingApp\Prospect\Models\Prospect;
use AdvisingApp\StudentDataModel\Filament\Resources\Students\StudentResource;
use AdvisingApp\StudentDataModel\Models\Student;
use AdvisingApp\Task\Enums\TaskStatus;
use AdvisingApp\Task\Models\Task;
use App\Filament\Resources\Users\UserResource;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\MorphToSelect;
use Filament\Forms\Components\MorphToSelect\Type;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Pages\ManageRelatedRecords;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ManageTasks extends ManageRelatedRecords
{
    protected static string $resource = ProjectResource::class;

    protected static string $relationship = 'tasks';

    public static function getNavigationLabel(): string
    {
        return 'Tasks';
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('title')
            ->columns([
                TextColumn::make('title')
                    ->sortable()
                    ->searchable()
                    ->icon(fn (Task $record) => $record->is_confidential ? 'heroicon-m-lock-closed' : null)
                    ->tooltip(fn (Task $record) => $record->is_confidential ? 'Confidential' : null),
            ])
            ->headerActions([
                CreateAction::make()
                    // ->authorize('create', Task::class)
                    ->schema([
                        Fieldset::make('Confidentiality')
                            ->schema([
                                Checkbox::make('is_confidential')
                                    ->label('Confidential')
                                    ->live()
                                    ->columnSpanFull(),
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
                            ->types([
                                Type::make(Student::class)
                                    ->titleAttribute('full_name'),
                                Type::make(Prospect::class)
                                    ->titleAttribute('full_name'),
                            ])
                            ->searchable(),
                    ])
                    ->modalHeading('Create Task')
                    ->modalSubmitActionLabel('Create Task'),
            ])
            ->recordActions([
                ViewAction::make()
                    ->extraModalFooterActions([
                        Action::make('mark_as_in_progress')
                            ->label('Mark as In Progress')
                            ->action(fn (Task $record) => $record->getStateMachine('status')->transitionTo(TaskStatus::InProgress))
                            ->cancelParentActions()
                            ->hidden(fn (Task $record) => $record->getStateMachine('status')->getStateTransitions()->doesntContain(TaskStatus::InProgress->value) || auth()->user()?->cannot("task.{$record->id}.update")),
                        Action::make('mark_as_completed')
                            ->label('Mark as Completed')
                            ->action(fn (Task $record) => $record->getStateMachine('status')->transitionTo(TaskStatus::Completed))
                            ->cancelParentActions()
                            ->hidden(fn (Task $record) => $record->getStateMachine('status')->getStateTransitions()->doesntContain(TaskStatus::Completed->value) || auth()->user()?->cannot("task.{$record->id}.update")),
                        Action::make('mark_as_canceled')
                            ->label('Mark as Canceled')
                            ->action(fn (Task $record) => $record->getStateMachine('status')->transitionTo(TaskStatus::Canceled))
                            ->cancelParentActions()
                            ->hidden(fn (Task $record) => $record->getStateMachine('status')->getStateTransitions()->doesntContain(TaskStatus::Canceled->value) || auth()->user()?->cannot("task.{$record->id}.update")),
                    ])
                    ->schema([
                        Grid::make()
                            ->schema([
                                TextEntry::make('is_confidential')
                                    ->label('')
                                    ->badge()
                                    ->formatStateUsing(fn ($state): string => $state ? 'Confidential' : '')
                                    ->visible(fn ($record): bool => $record->is_confidential),
                                TextEntry::make('title'),
                                TextEntry::make('description'),
                                TextEntry::make('assignedTo.name')
                                    ->label('Assigned To')
                                    ->url(
                                        fn (Task $record) => $record->assignedTo
                                        ? UserResource::getUrl('view', ['record' => $record->assignedTo])
                                        : null
                                    )
                                    ->default('Unassigned'),
                                TextEntry::make('concern.full_name')
                                    ->label('Related To')
                                    ->getStateUsing(
                                        fn (Task $record): ?string => $record->concern ? match ($record->concern::class) {
                                            Student::class => $record->concern->full_name,
                                            Prospect::class => $record->concern->full_name,
                                            default => null,
                                        } : null
                                    )
                                    ->url(
                                        fn (Task $record): string|null => $record->concern ? match ($record->concern::class) {
                                            Student::class => StudentResource::getUrl('view', ['record' => $record->concern]),
                                            Prospect::class => ProspectResource::getUrl('view', ['record' => $record->concern]),
                                            default => null,
                                        } : null
                                    )
                                    ->default('Unrelated'),
                                Fieldset::make('metadata')
                                    ->label('Metadata')
                                    ->schema([
                                        TextEntry::make('status')
                                            ->formatStateUsing(fn (TaskStatus $state): string => str($state->value)->title()->headline())
                                            ->badge(),
                                        TextEntry::make('due')
                                            ->label('Due Date')
                                            ->default('N/A'),
                                        TextEntry::make('createdBy.name')
                                            ->label('Created By')
                                            ->default('N/A')
                                            ->url(
                                                fn (Task $record) => $record->createdBy
                                                ? UserResource::getUrl('view', ['record' => $record->createdBy])
                                                : null
                                            ),
                                    ]),
                            ]),
                    ])
                    ->authorize('view', Task::class),
                EditAction::make()
                    ->schema([
                        Fieldset::make('Confidentiality')
                            ->schema([
                                Checkbox::make('is_confidential')
                                    ->label('Confidential')
                                    ->live()
                                    ->columnSpanFull(),
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
                            ->types([
                                Type::make(Student::class)
                                    ->titleAttribute('full_name'),
                                Type::make(Prospect::class)
                                    ->titleAttribute('full_name'),
                            ])
                            ->searchable(),
                    ])
                    ->authorize('update', Task::class),
                DeleteAction::make()
                    ->authorize('delete', Task::class),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->authorize(fn () => auth()->user()->can('update', $this->getOwnerRecord())),
                ]),
            ]);
    }
}
