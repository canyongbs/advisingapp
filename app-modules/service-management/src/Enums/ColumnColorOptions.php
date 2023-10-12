<?php

namespace Assist\ServiceManagement\Enums;

// TODO This might belong in a more generalized space so we can re-use this across modules
enum ColumnColorOptions: string
{
    case Success = 'success';

    case Danger = 'danger';

    case Warning = 'warning';

    case Info = 'info';

    case Primary = 'primary';

    case Gray = 'gray';
}
