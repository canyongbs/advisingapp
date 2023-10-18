<?php

namespace Assist\ServiceManagement\Enums;

enum SystemServiceRequestClassification: string
{
    case Open = 'open';

    case InProgress = 'in_progress';

    case Closed = 'closed';

    case Custom = 'custom';
}
