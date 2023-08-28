<?php

namespace Assist\Task\Enums;

enum TaskStatus: string
{
    case PENDING = 'pending';

    case IN_PROGRESS = 'in_progress';

    case COMPLETED = 'completed';

    case CANCELLED = 'canceled';
}
