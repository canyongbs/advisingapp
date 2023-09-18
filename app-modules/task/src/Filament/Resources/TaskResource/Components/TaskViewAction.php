<?php

namespace Assist\Task\Filament\Resources\TaskResource\Components;

use Assist\Task\Models\Task;
use Assist\Task\Enums\TaskStatus;
use Filament\Tables\Actions\Action;
use Assist\Prospect\Models\Prospect;
use Filament\Infolists\Components\Grid;
use Filament\Tables\Actions\ViewAction;
use App\Filament\Resources\UserResource;
use Filament\Infolists\Components\Split;
use Assist\AssistDataModel\Models\Student;
use Filament\Infolists\Components\Fieldset;
use Filament\Infolists\Components\TextEntry;
use Assist\Prospect\Filament\Resources\ProspectResource;
use Assist\AssistDataModel\Filament\Resources\StudentResource;

class TaskViewAction extends ViewAction
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->extraModalFooterActions(
            [
                Action::make('mark_as_in_progress')
                    ->label('Mark as In Progress')
                    ->action(fn (Task $record) => $record->getStateMachine('status')->transitionTo(TaskStatus::IN_PROGRESS))
                    ->cancelParentActions()
                    ->hidden(fn (Task $record) => $record->getStateMachine('status')->getStateTransitions()->doesntContain(TaskStatus::IN_PROGRESS->value) || auth()?->user()?->cannot("task.{$record->id}.update")),
                Action::make('mark_as_completed')
                    ->label('Mark as Completed')
                    ->action(fn (Task $record) => $record->getStateMachine('status')->transitionTo(TaskStatus::COMPLETED))
                    ->cancelParentActions()
                    ->hidden(fn (Task $record) => $record->getStateMachine('status')->getStateTransitions()->doesntContain(TaskStatus::COMPLETED->value) || auth()?->user()?->cannot("task.{$record->id}.update")),
            ]
        )
            ->infolist(
                [
                    Split::make(
                        [
                            Grid::make()
                                ->schema(
                                    [
                                        TextEntry::make('description')
                                            ->columnSpanFull(),
                                        TextEntry::make('assignedTo.name')
                                            ->label('Assigned To')
                                            ->url(fn (Task $record) => $record->assignedTo ? UserResource::getUrl('view', ['record' => $record->assignedTo]) : null)
                                            ->default('Unassigned'),
                                        TextEntry::make('concern.display_name')
                                            ->label('Concern')
                                            ->getStateUsing(fn (Task $record) => $record->concern->{$record->concern::displayNameKey()})
                                            ->url(fn (Task $record) => match ($record->concern ? $record->concern::class : null) {
                                                Student::class => StudentResource::getUrl('view', ['record' => $record->concern]),
                                                Prospect::class => ProspectResource::getUrl('view', ['record' => $record->concern]),
                                                default => null,
                                            })
                                            ->default('Unrelated'),
                                    ]
                                ),
                            Fieldset::make('metadata')
                                ->label('Metadata')
                                ->schema(
                                    [
                                        TextEntry::make('status')
                                            ->formatStateUsing(fn (TaskStatus $state): string => str($state->value)->title()->headline())
                                            ->badge()
                                            ->color(fn (Task $record) => $record->status->getTableColor()),
                                        TextEntry::make('due')
                                            ->label('Due Date')
                                            ->default('N/A'),
                                        TextEntry::make('createdBy.name')
                                            ->label('Created By')
                                            ->default('N/A')
                                            ->url(fn (Task $record) => $record->createdBy ? UserResource::getUrl('view', ['record' => $record->createdBy]) : null),
                                    ]
                                ),
                        ]
                    )->from('md'),
                ]
            );
    }
}
