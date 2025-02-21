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

use AdvisingApp\Notification\Enums\EmailMessageEventType;
use AdvisingApp\Notification\Models\EmailMessage;
use AdvisingApp\Notification\Notifications\Attributes\SystemNotification;
use AdvisingApp\Notification\Notifications\Messages\MailMessage;
use AdvisingApp\Notification\Tests\Fixtures\TestEmailNotification;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

it('will create an EmailMessage for the notification', function () {
    $notifiable = User::factory()->create();

    $notification = new TestEmailNotification();

    $notifiable->notify($notification);

    $emailMessages = EmailMessage::all();

    expect($emailMessages->count())->toBe(1);
    expect($emailMessages->first()->notification_class)->toBe(TestEmailNotification::class);
});

it('will not send emails in demo mode and record a BlockedByDemoMode event', function () {
    $user = User::factory()->create();

    $tenantConfig = Tenant::current()->config;
    $tenantConfig->mail->isDemoModeEnabled = true;
    Tenant::current()->update([
        'config' => $tenantConfig,
    ]);

    $notification = new TestEmailNotification();
    $user->notify($notification);

    $emailMessages = EmailMessage::query()
        ->with('events')
        ->get();

    expect($emailMessages->count())->toBe(1);
    expect($emailMessages->first()->events->count())->toBe(1);
    expect($emailMessages->first()->events->first()->type)->toBe(EmailMessageEventType::BlockedByDemoMode);
});

it('will send system notifications in demo mode', function () {
    $user = User::factory()->create();

    $tenantConfig = Tenant::current()->config;
    $tenantConfig->mail->isDemoModeEnabled = true;
    $tenantConfig->mail->isExcludingSystemNotificationsFromDemoMode = true;
    Tenant::current()->update([
        'config' => $tenantConfig,
    ]);

    $notification = new TestSystemNotification();
    $user->notify($notification);

    $emailMessages = EmailMessage::query()
        ->with('events')
        ->get();

    expect($emailMessages->count())->toBe(1);
    expect($emailMessages->first()->events->count())->toBe(1);
    expect($emailMessages->first()->events->first()->type)->toBe(EmailMessageEventType::Dispatched);
});

it('will not send system notifications in demo mode when system notifications are not excluded', function () {
    $user = User::factory()->create();

    $tenantConfig = Tenant::current()->config;
    $tenantConfig->mail->isDemoModeEnabled = true;
    $tenantConfig->mail->isExcludingSystemNotificationsFromDemoMode = false;
    Tenant::current()->update([
        'config' => $tenantConfig,
    ]);

    $notification = new TestSystemNotification();
    $user->notify($notification);

    $emailMessages = EmailMessage::query()
        ->with('events')
        ->get();

    expect($emailMessages->count())->toBe(1);
    expect($emailMessages->first()->events->count())->toBe(1);
    expect($emailMessages->first()->events->first()->type)->toBe(EmailMessageEventType::BlockedByDemoMode);
});

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
