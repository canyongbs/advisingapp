<?php

namespace Assist\Task\Filament\Resources\TaskResource\Components;

use Assist\Task\Models\Task;
use Assist\Task\Enums\TaskStatus;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ViewAction;
use Assist\Task\Filament\Concerns\TaskViewActionInfoList;

class TaskViewAction extends ViewAction
{
    use TaskViewActionInfoList;

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
            ->infolist($this->taskInfoList());
    }
}
