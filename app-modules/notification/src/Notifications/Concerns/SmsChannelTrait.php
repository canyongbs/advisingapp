<?php

namespace AdvisingApp\Notification\Notifications\Concerns;

use AdvisingApp\Notification\Notifications\Channels\SmsChannel;
use AdvisingApp\Notification\Notifications\Messages\TwilioMessage;

trait SmsChannelTrait
{
    use ChannelTrait;

    public static $channel = SmsChannel::class;

    abstract public function toSms(object $notifiable): TwilioMessage;
}
