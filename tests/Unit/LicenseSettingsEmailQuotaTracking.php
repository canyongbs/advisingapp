<?php

use App\Models\User;
use App\Settings\LicenseSettings;
use Illuminate\Mail\Events\MessageSent;

use function Pest\Laravel\assertDatabaseCount;

use AdvisingApp\Notification\Models\OutboundDeliverable;
use AdvisingApp\Notification\Enums\NotificationDeliveryStatus;
use AdvisingApp\IntegrationAwsSesEventHandling\Settings\SesSettings;

it('An email is allowed to be sent if there is available quota and it\'s quota usage is tracked', function () {
    Event::fake(MessageSent::class);

    $configurationSet = 'test';

    $settings = app(SesSettings::class);
    $settings->configuration_set = $configurationSet;
    $settings->save();

    $notifiable = User::factory()->create();

    $notification = new Tests\Unit\TestEmailNotification();

    $notifiable->notify($notification);

    Event::assertDispatched(
        function (MessageSent $event) use ($configurationSet) {
            assertDatabaseCount(OutboundDeliverable::class, 1);

            $outboundDeliverable = OutboundDeliverable::first();

            return $event->message->getHeaders()->get('X-SES-CONFIGURATION-SET')->getBody() === $configurationSet
                && $event->message->getHeaders()->get('X-SES-MESSAGE-TAGS')->getBody() === 'outbound_deliverable_id=' . $outboundDeliverable->getKey()
                && $outboundDeliverable->quota_usage === 1;
        }
    );
});

it('An email is prevented from being sent if there is no available quota', function () {
    Event::fake(MessageSent::class);

    $configurationSet = 'test';

    $settings = app(SesSettings::class);
    $settings->configuration_set = $configurationSet;
    $settings->save();

    $licenseSettings = app(LicenseSettings::class);

    $licenseSettings->data->limits->emails = 0;
    $licenseSettings->save();

    $notifiable = User::factory()->create();

    $notification = new Tests\Unit\TestEmailNotification();

    $notifiable->notify($notification);

    Event::assertNotDispatched(MessageSent::class);

    assertDatabaseCount(OutboundDeliverable::class, 1);

    $outboundDeliverable = OutboundDeliverable::first();

    expect($outboundDeliverable->delivery_status)->toBe(NotificationDeliveryStatus::RateLimited);
});
