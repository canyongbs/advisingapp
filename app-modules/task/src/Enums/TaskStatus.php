<?php

namespace Assist\Task\Enums;

use Bvtterfly\ModelStateMachine\Attributes\InitialState;
use Bvtterfly\ModelStateMachine\Attributes\AllowTransitionTo;

enum TaskStatus: string
{
    #[InitialState]
    #[AllowTransitionTo(self::InProgress)]
    #[AllowTransitionTo(self::Cancelled)]
    case Pending = 'pending';

    #[AllowTransitionTo(self::Completed)]
    #[AllowTransitionTo(self::Cancelled)]
    case InProgress = 'in_progress';

    case Completed = 'completed';

    case Cancelled = 'canceled';

    public function getTableColor(): string
    {
        return match ($this) {
            self::Pending => 'gray',
            self::InProgress => 'primary',
            self::Completed => 'success',
            self::Cancelled => 'danger',
        };
    }

    public function displayName(): string
    {
        return match ($this) {
            self::Pending => 'Pending',
            self::InProgress => 'In Progress',
            self::Completed => 'Completed',
            self::Cancelled => 'Cancelled',
        };
    }
}
