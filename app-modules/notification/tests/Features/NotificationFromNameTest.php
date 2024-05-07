<?php

use App\Models\User;
use App\Models\NotificationSetting;
use Illuminate\Support\Facades\Notification;
use AdvisingApp\Notification\Tests\Features\TestEmailSettingFromNameNotification;

it('sets the mail from name based on settings fromName if set', function () {
    Notification::fake();

    $user = User::factory()->create();

    $notificationSetting = NotificationSetting::make();

    $notificationSetting->from_name = fake()->name();

    $notification = new TestEmailSettingFromNameNotification($notificationSetting);

    $user->notify($notification);

    Notification::assertSentTo(
        $user,
        function (TestEmailSettingFromNameNotification $notification, array $channels) use ($notificationSetting, $user) {
            $mailMessage = $notification->toMail($user);

            return $mailMessage->from[1] === $notificationSetting->from_name;
        }
    );
});
