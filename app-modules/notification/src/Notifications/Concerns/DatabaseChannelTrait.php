<?php

namespace AdvisingApp\Notification\Notifications\Concerns;

use AdvisingApp\Notification\Notifications\Channels\DatabaseChannel;

trait DatabaseChannelTrait
{
    use ChannelTrait;

    public static function getDatabaseChannel(): string
    {
        return DatabaseChannel::class;
    }
}
