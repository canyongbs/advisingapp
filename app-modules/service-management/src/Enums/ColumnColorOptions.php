<?php

namespace Assist\ServiceManagement\Enums;

// TODO This might belong in a more generalized space
// I'm now also using this with the InteractionStatus
enum ColumnColorOptions: string
{
    case SUCCESS = 'success';

    case DANGER = 'danger';

    case WARNING = 'warning';

    case INFO = 'info';

    case PRIMARY = 'primary';

    case GRAY = 'gray';
}
