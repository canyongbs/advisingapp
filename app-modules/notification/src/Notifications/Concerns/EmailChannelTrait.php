<?php

namespace AdvisingApp\Notification\Notifications\Concerns;

use AdvisingApp\Notification\Notifications\Channels\EmailChannel;

trait EmailChannelTrait
{
    use ChannelTrait;

    public static $channel = EmailChannel::class;
}
