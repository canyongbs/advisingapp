<?php

namespace Assist\ServiceManagement\Enums;

enum ServiceRequestUpdateDirection: string
{
    case Inbound = 'inbound';

    case Outbound = 'outbound';
}
