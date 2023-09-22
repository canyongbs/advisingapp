<?php

namespace Assist\Task\Enums;

use Bvtterfly\ModelStateMachine\Attributes\InitialState;
use Bvtterfly\ModelStateMachine\Attributes\AllowTransitionTo;

enum TaskStatus: string
{
    #[InitialState]
    #[AllowTransitionTo(self::IN_PROGRESS)]
    #[AllowTransitionTo(self::CANCELLED)]
    case PENDING = 'pending';

    #[AllowTransitionTo(self::COMPLETED)]
    #[AllowTransitionTo(self::CANCELLED)]
    case IN_PROGRESS = 'in_progress';

    case COMPLETED = 'completed';

    case CANCELLED = 'canceled';

    public function getTableColor(): string
    {
        return match ($this) {
            self::PENDING => 'gray',
            self::IN_PROGRESS => 'primary',
            self::COMPLETED => 'success',
            self::CANCELLED => 'danger',
        };
    }

    public function displayName(): string
    {
        return match ($this) {
            self::PENDING => 'Pending',
            self::IN_PROGRESS => 'In Progress',
            self::COMPLETED => 'Completed',
            self::CANCELLED => 'Cancelled',
        };
    }
}
