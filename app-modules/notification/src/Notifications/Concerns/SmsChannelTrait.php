<?php

namespace AdvisingApp\Notification\Notifications\Concerns;

use AdvisingApp\Notification\Notifications\Channels\SmsChannel;

trait SmsChannelTrait
{
    use ChannelTrait;

    public static $channel = SmsChannel::class;
}
