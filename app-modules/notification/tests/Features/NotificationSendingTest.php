<?php

/*
<COPYRIGHT>

    Copyright © 2016-2024, Canyon GBS LLC. All rights reserved.

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

use App\Models\User;
use App\Models\Authenticatable;
use Tests\Unit\TestEmailNotification;
use AdvisingApp\Notification\Enums\NotificationChannel;
use AdvisingApp\Notification\Models\OutboundDeliverable;
use AdvisingApp\Notification\Notifications\BaseNotification;
use AdvisingApp\Notification\Notifications\EmailNotification;
use AdvisingApp\Notification\Notifications\DatabaseNotification;
use AdvisingApp\Notification\Notifications\Messages\MailMessage;
use Filament\Notifications\Notification as FilamentNotification;
use AdvisingApp\Notification\Notifications\Concerns\EmailChannelTrait;
use AdvisingApp\Notification\Notifications\Concerns\DatabaseChannelTrait;

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

it('will not count emails sent to Super Admin Users against quota usage', function () {
    // Given that we have a super admin user
    $user = User::factory()->create();
    $nonSuperAdminUser = User::factory()->create();

    $user->assignRole(Authenticatable::SUPER_ADMIN_ROLE);

    // And they are sent a deliverable of any kind
    $notification = new TestMultipleChannelNotification();
    $user->notify($notification);

    $notification = new TestMultipleChannelNotification();
    $nonSuperAdminUser->notify($notification);

    // Then the quota usage for the super admin user should be 0
    expect(OutboundDeliverable::where('recipient_id', $user->id)->where('channel', NotificationChannel::Email)->first()->quota_usage)->toBe(0);
    expect(OutboundDeliverable::where('recipient_id', $nonSuperAdminUser->id)->where('channel', NotificationChannel::Email)->first()->quota_usage)->toBe(1);
});

class TestMultipleChannelNotification extends BaseNotification implements EmailNotification, DatabaseNotification
{
    use EmailChannelTrait;
    use DatabaseChannelTrait;

    public function toEmail(object $notifiable): MailMessage
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
