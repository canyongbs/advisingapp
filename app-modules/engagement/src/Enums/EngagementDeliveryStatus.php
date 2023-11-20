<?php

namespace Assist\Engagement\Enums;

enum EngagementDeliveryStatus: string
{
    case Awaiting = 'awaiting';
    case Successful = 'successful';
    case Failed = 'failed';
}
