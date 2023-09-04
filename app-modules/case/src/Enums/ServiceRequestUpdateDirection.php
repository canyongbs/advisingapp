<?php

namespace Assist\Case\Enums;

enum ServiceRequestUpdateDirection: string
{
    case Inbound = 'inbound';

    case Outbound = 'outbound';
}
