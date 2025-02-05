<?php

/*
<COPYRIGHT>

    Copyright © 2016-2025, Canyon GBS LLC. All rights reserved.

    Advising App™ is licensed under the Elastic License 2.0. For more details,
    see https://github.com/canyongbs/advisingapp/blob/main/LICENSE.

    Notice:

    - You may not provide the software to third parties as a hosted or managed
      service, where the service provides users with access to any substantial set of
      the features or functionality of the software.
    - You may not move, change, disable, or circumvent the license key functionality
      in the software, and you may not remove or obscure any functionality in the
      software that is protected by the license key.
    - You may not alter, remove, or obscure any licensing, copyright, or other notices
      of the licensor in the software. Any use of the licensor’s trademarks is subject
      to applicable law.
    - Canyon GBS LLC respects the intellectual property rights of others and expects the
      same in return. Canyon GBS™ and Advising App™ are registered trademarks of
      Canyon GBS LLC, and we are committed to enforcing and protecting our trademarks
      vigorously.
    - The software solution, including services, infrastructure, and code, is offered as a
      Software as a Service (SaaS) by Canyon GBS LLC.
    - Use of this software implies agreement to the license terms and conditions as stated
      in the Elastic License 2.0.

    For more information or inquiries please visit our website at
    https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

use AdvisingApp\Notification\Enums\NotificationChannel;
use AdvisingApp\Notification\Enums\NotificationDeliveryStatus;
use AdvisingApp\Notification\Models\OutboundDeliverable;
use AdvisingApp\Notification\Notifications\Attributes\SystemNotification;
use AdvisingApp\Notification\Notifications\Messages\MailMessage;
use AdvisingApp\Notification\Tests\Fixtures\TestEmailNotification;
use App\Models\Tenant;
use App\Models\User;
use Filament\Notifications\Notification as FilamentNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

it('will create an outbound deliverable for the outbound notification', function () {
    $notifiable = User::factory()->create();

    $notification = new TestEmailNotification();

    $notifiable->notify($notification);

    expect(OutboundDeliverable::count())->toBe(1);
    expect(OutboundDeliverable::first()->notification_class)->toBe(TestEmailNotification::class);
});

it('will create an outbound deliverable for each of the channels that the notification specifies', function () {
    $notifiable = User::factory()->create();

    $notification = new TestMultipleChannelNotification();

    $notifiable->notify($notification);

    expect(OutboundDeliverable::count())->toBe(2);
    expect(OutboundDeliverable::where('channel', NotificationChannel::Email)->count())->toBe(1);
    expect(OutboundDeliverable::where('channel', NotificationChannel::Database)->count())->toBe(1);
});

it('will not send emails in demo mode and mark the outbound deliverable as blocked', function () {
    expect(OutboundDeliverable::count())->toBe(0);

    $user = User::factory()->create();

    $tenantConfig = Tenant::current()->config;
    $tenantConfig->mail->isDemoModeEnabled = true;
    Tenant::current()->update([
        'config' => $tenantConfig,
    ]);

    $notification = new TestNotification();
    $user->notify($notification);

    expect(OutboundDeliverable::count())->toBe(1);
    expect(OutboundDeliverable::first()->delivery_status)->toBe(NotificationDeliveryStatus::BlockedByDemoMode);
});

it('will send system notifications in demo mode', function () {
    expect(OutboundDeliverable::count())->toBe(0);

    $user = User::factory()->create();

    $tenantConfig = Tenant::current()->config;
    $tenantConfig->mail->isDemoModeEnabled = true;
    $tenantConfig->mail->isExcludingSystemNotificationsFromDemoMode = true;
    Tenant::current()->update([
        'config' => $tenantConfig,
    ]);

    $notification = new TestSystemNotification();
    $user->notify($notification);

    expect(OutboundDeliverable::count())->toBe(1);
    expect(OutboundDeliverable::first()->delivery_status)->not->toBe(NotificationDeliveryStatus::BlockedByDemoMode);
});

it('will not send system notifications in demo mode when system notifications are not excluded', function () {
    expect(OutboundDeliverable::count())->toBe(0);

    $user = User::factory()->create();

    $tenantConfig = Tenant::current()->config;
    $tenantConfig->mail->isDemoModeEnabled = true;
    $tenantConfig->mail->isExcludingSystemNotificationsFromDemoMode = false;
    Tenant::current()->update([
        'config' => $tenantConfig,
    ]);

    $notification = new TestSystemNotification();
    $user->notify($notification);

    expect(OutboundDeliverable::count())->toBe(1);
    expect(OutboundDeliverable::first()->delivery_status)->toBe(NotificationDeliveryStatus::BlockedByDemoMode);
});

class TestNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return MailMessage::make()
            ->subject('Test Subject')
            ->greeting('Test Greeting')
            ->content('This is a test email');
    }
}

class TestMultipleChannelNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return MailMessage::make()
            ->subject('Test Subject')
            ->greeting('Test Greeting')
            ->content('This is a test email');
    }

    public function toDatabase(object $notifiable): array
    {
        return FilamentNotification::make()
            ->success()
            ->title('This is a test database notification.')
            ->body('This is the content of your test database notification')
            ->getDatabaseMessage();
    }
}

#[SystemNotification]
class TestSystemNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return MailMessage::make()
            ->subject('Test Subject')
            ->greeting('Test Greeting')
            ->content('This is a test email');
    }
}
