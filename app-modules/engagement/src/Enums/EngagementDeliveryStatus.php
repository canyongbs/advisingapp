<?php

namespace Assist\Engagement\Enums;

enum EngagementDeliveryStatus: string
{
    case AWAITING = 'awaiting';
    case SUCCESSFUL = 'successful';
    case FAILED = 'failed';
}
