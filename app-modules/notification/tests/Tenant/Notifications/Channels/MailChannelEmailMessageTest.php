<?php

/*
<COPYRIGHT>

    Copyright © 2016-2026, Canyon GBS Inc. All rights reserved.

    Advising App® is licensed under the Elastic License 2.0. For more details,
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
    - Canyon GBS Inc. respects the intellectual property rights of others and expects the
      same in return. Canyon GBS® and Advising App® are registered trademarks of
      Canyon GBS Inc., and we are committed to enforcing and protecting our trademarks
      vigorously.
    - The software solution, including services, infrastructure, and code, is offered as a
      Software as a Service (SaaS) by Canyon GBS Inc.
    - Use of this software implies agreement to the license terms and conditions as stated
      in the Elastic License 2.0.

    For more information or inquiries please visit our website at
    https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

use AdvisingApp\Notification\Enums\EmailMessageEventType;
use AdvisingApp\Notification\Enums\EmailType;
use AdvisingApp\Notification\Models\EmailMessage;
use AdvisingApp\Notification\Notifications\Attributes\SystemNotification;
use AdvisingApp\Notification\Notifications\Contracts\HasEmailType;
use AdvisingApp\Notification\Notifications\Messages\MailMessage;
use AdvisingApp\Notification\Tests\Fixtures\TestEmailNotification;
use AdvisingApp\Prospect\Models\Prospect;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Events\MessageSent;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Event;

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

it('sets email_type to marketing when notification implements HasEmailType returning Marketing', function () {
    $notifiable = Prospect::factory()->create();

    $notification = new TestMarketingNotification();

    $notifiable->notify($notification);

    $emailMessage = EmailMessage::first();

    expect($emailMessage)->not->toBeNull()
        ->and($emailMessage->email_type)->toBe(EmailType::Marketing->value);
});

it('sets email_type to transactional when notification implements HasEmailType returning Transactional', function () {
    $notifiable = Prospect::factory()->create();

    $notification = new TestTransactionalNotification();

    $notifiable->notify($notification);

    $emailMessage = EmailMessage::first();

    expect($emailMessage)->not->toBeNull()
        ->and($emailMessage->email_type)->toBe(EmailType::Transactional->value);
});

it('defaults email_type to transactional when notification does not implement HasEmailType', function () {
    $notifiable = User::factory()->create();

    $notification = new TestEmailNotification();

    $notifiable->notify($notification);

    $emailMessage = EmailMessage::first();

    expect($emailMessage)->not->toBeNull()
        ->and($emailMessage->email_type)->toBe(EmailType::Transactional->value);
});

it('includes unsubscribeUrl in viewData for marketing email', function () {
    Event::fake(MessageSent::class);

    $notifiable = Prospect::factory()->create();

    $notification = new TestMarketingNotification();

    $notifiable->notify($notification);

    Event::assertDispatched(function (MessageSent $event) {
        $htmlBody = $event->message->getHtmlBody();

        return str_contains($htmlBody, 'Unsubscribe')
            && str_contains($htmlBody, '/unsubscribe');
    });
});

it('does not include unsubscribeUrl in viewData for transactional email', function () {
    Event::fake(MessageSent::class);

    $notifiable = Prospect::factory()->create();

    $notification = new TestTransactionalNotification();

    $notifiable->notify($notification);

    Event::assertDispatched(function (MessageSent $event) {
        $htmlBody = $event->message->getHtmlBody();

        return ! str_contains($htmlBody, '/unsubscribe');
    });
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

class TestMarketingNotification extends Notification implements ShouldQueue, HasEmailType
{
    use Queueable;

    public function getEmailType(): string
    {
        return EmailType::Marketing->value;
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return MailMessage::make()
            ->subject('Marketing Campaign')
            ->greeting('Hello!')
            ->content('This is a marketing email.');
    }
}

class TestTransactionalNotification extends Notification implements ShouldQueue, HasEmailType
{
    use Queueable;

    public function getEmailType(): string
    {
        return EmailType::Transactional->value;
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return MailMessage::make()
            ->subject('Password Reset')
            ->greeting('Hello!')
            ->content('This is a transactional email.');
    }
}
