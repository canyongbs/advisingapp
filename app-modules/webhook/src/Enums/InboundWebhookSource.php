<?php

namespace Assist\Webhook\Enums;

enum InboundWebhookSource: string
{
    case Twilio = 'twilio';

    case AwsSns = 'aws_sns';
}
