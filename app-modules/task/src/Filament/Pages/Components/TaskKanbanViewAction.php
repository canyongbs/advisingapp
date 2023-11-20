<?php

/*
<COPYRIGHT>

Copyright © 2022-2023, Canyon GBS LLC

All rights reserved.

This file is part of a project developed using Laravel, which is an open-source framework for PHP.
Canyon GBS LLC acknowledges and respects the copyright of Laravel and other open-source
projects used in the development of this solution.

This project is licensed under the Affero General Public License (AGPL) 3.0.
For more details, see https://github.com/canyongbs/assistbycanyongbs/blob/main/LICENSE.

Notice:
- The copyright notice in this file and across all files and applications in this
 repository cannot be removed or altered without violating the terms of the AGPL 3.0 License.
- The software solution, including services, infrastructure, and code, is offered as a
 Software as a Service (SaaS) by Canyon GBS LLC.
- Use of this software implies agreement to the license terms and conditions as stated
 in the AGPL 3.0 License.

For more information or inquiries please visit our website at
https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

namespace Assist\Task\Filament\Pages\Components;

use Assist\Task\Models\Task;
use Filament\Actions\Action;
use Filament\Actions\ViewAction;
use Assist\Task\Enums\TaskStatus;
use Assist\Task\Filament\Concerns\TaskViewActionInfoList;

class TaskKanbanViewAction extends ViewAction
{
    use TaskViewActionInfoList;

    protected function setUp(): void
    {
        parent::setUp();

        $this->extraModalFooterActions([
            Action::make('mark_as_in_progress')
                ->label('Mark as In Progress')
                ->action(fn (Task $record) => $record->getStateMachine('status')->transitionTo(TaskStatus::InProgress))
                ->cancelParentActions()
                ->hidden(fn (Task $record) => $record->getStateMachine('status')->getStateTransitions()->doesntContain(TaskStatus::InProgress->value) || auth()?->user()?->cannot("task.{$record->id}.update")),
            Action::make('mark_as_completed')
                ->label('Mark as Completed')
                ->action(fn (Task $record) => $record->getStateMachine('status')->transitionTo(TaskStatus::Completed))
                ->cancelParentActions()
                ->hidden(fn (Task $record) => $record->getStateMachine('status')->getStateTransitions()->doesntContain(TaskStatus::Completed->value) || auth()?->user()?->cannot("task.{$record->id}.update")),
        ])->infolist($this->taskInfoList());
    }
}
