<?php

namespace Assist\Interaction\Enums;

enum InteractionStatusColorOptions: string
{
    case Success = 'success';

    case Danger = 'danger';

    case Warning = 'warning';

    case Info = 'info';

    case Primary = 'primary';

    case Gray = 'gray';
}
