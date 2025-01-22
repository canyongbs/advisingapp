<?php

namespace AdvisingApp\Notification\Notifications;

use Illuminate\Notifications\ChannelManager as BaseChannelManager;
use Illuminate\Support\Collection;

class ChannelManager extends BaseChannelManager
{
    /**
     * Send the given notification to the given notifiable entities.
     *
     * @param  Collection|array|mixed  $notifiables
     * @param  mixed  $notification
     *
     * @return void
     */
    public function send($notifiables, $notification)
    {
        if (property_exists($notification, 'queue')) {
            $notification->queue ??= config('queue.outbound_communication_queue');
        }

        parent::send($notifiables, $notification);
    }
}
