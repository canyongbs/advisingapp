<?php

namespace AdvisingApp\Notification\Models\Contracts;

interface NotifiableInterface
{
    public function notify($instance);

    public function notifyNow($instance, array $channels = null);

    public function routeNotificationFor($driver, $notification = null);

    public function notifications();

    public function readNotifications();

    public function unreadNotifications();
}
