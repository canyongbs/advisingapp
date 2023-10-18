<?php

namespace Assist\Prospect\Enums;

enum SystemProspectClassification: string
{
    case New = 'new';

    case Assigned = 'assigned';

    case InProgress = 'in_progress';

    case Converted = 'converted';

    case Recycled = 'recycled';

    case Dead = 'dead';

    case Custom = 'custom';
}
