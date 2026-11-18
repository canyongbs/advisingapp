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

use AdvisingApp\IntegrationAwsSesEventHandling\Actions\DispatchSesEvent;
use AdvisingApp\IntegrationAwsSesEventHandling\DataTransferObjects\SesEventData;
use AdvisingApp\IntegrationAwsSesEventHandling\Events\SesBounceEvent;
use AdvisingApp\IntegrationAwsSesEventHandling\Listeners\HandleSesBounceEvent;
use AdvisingApp\Notification\Enums\EmailMessageEventType;
use AdvisingApp\Notification\Models\EmailMessage;
use App\Models\Tenant;
use Illuminate\Support\Carbon;

use function Tests\loadFixtureFromModule;

/**
 * @return array<string, mixed>
 */
$sesNotificationFor = function (Tenant $tenant, EmailMessage $emailMessage, string $event, string $snsMessageId = '22b80b92-fdea-4c2c-8f9d-bdfb0c7bf324'): array {
    $snsData = loadFixtureFromModule('integration-aws-ses-event-handling', 'sns-notification');
    $messageContent = loadFixtureFromModule('integration-aws-ses-event-handling', $event);
    data_set($messageContent, 'mail.tags.app_message_id.0', $emailMessage->getKey());
    data_set($messageContent, 'mail.tags.tenant_id.0', $tenant->getKey());
    $snsData['MessageId'] = $snsMessageId;
    $snsData['Message'] = json_encode($messageContent);

    return $snsData;
};

/**
 * @param array<string, mixed> $snsData
 */
$dispatchSesNotification = function (array $snsData): void {
    app(DispatchSesEvent::class)(SesEventData::createFromSnsEnvelope($snsData), $snsData['MessageId']);
};

it('correctly handles the incoming SES event', function (string $event, EmailMessageEventType $eventType) use ($sesNotificationFor, $dispatchSesNotification): void {
    /** @var Tenant $tenant */
    $tenant = Tenant::query()->first();

    $emailMessage = $tenant->execute(function () {
        return EmailMessage::factory()->create();
    });

    $tenant->execute(function () use ($emailMessage): void {
        expect($emailMessage->events()->count())->toBe(0);
    });

    // And we receive some sort of SES event when attempting to deliver
    $dispatchSesNotification($sesNotificationFor($tenant, $emailMessage, $event));

    $tenant->execute(function () use ($emailMessage, $eventType): void {
        // The email message should have the apppriate email message event created based on the event
        $emailMessage->refresh();

        expect($emailMessage->events()->count())->toBe(1);

        $event = $emailMessage->events()->first();

        expect($event->type)->toBe($eventType);
    });
})->with([
    'HandleSesBounceEvent' => [
        'Bounce',
        EmailMessageEventType::Bounce,
    ],
    'HandleSesClickEvent' => [
        'Click',
        EmailMessageEventType::Click,
    ],
    'HandleSesComplaintEvent' => [
        'Complaint',
        EmailMessageEventType::Complaint,
    ],
    'HandleSesDeliveryEvent' => [
        'Delivery',
        EmailMessageEventType::Delivery,
    ],
    'HandleSesDeliveryDelayEvent' => [
        'DeliveryDelay',
        EmailMessageEventType::DeliveryDelay,
    ],
    'HandleSesOpenEvent' => [
        'Open',
        EmailMessageEventType::Open,
    ],
    'HandleSesRejectEvent' => [
        'Reject',
        EmailMessageEventType::Reject,
    ],
    'HandleSesRenderingFailureEvent' => [
        'RenderingFailure',
        EmailMessageEventType::RenderingFailure,
    ],
    'HandleSesSendEvent' => [
        'Send',
        EmailMessageEventType::Send,
    ],
    'HandleSesSubscriptionEvent' => [
        'Subscription',
        EmailMessageEventType::Subscription,
    ],
]);

it('records the SES send time for events that have no timestamp of their own', function (string $event) use ($sesNotificationFor, $dispatchSesNotification): void {
    $tenant = Tenant::query()->firstOrFail();

    $emailMessage = $tenant->execute(fn () => EmailMessage::factory()->create());

    $snsData = $sesNotificationFor($tenant, $emailMessage, $event);

    $dispatchSesNotification($snsData);

    $tenant->execute(function () use ($emailMessage, $snsData): void {
        expect($emailMessage->events()->sole()->occurred_at->toDateTimeString())
            ->toBe(Carbon::parse(data_get(json_decode($snsData['Message'], true), 'mail.timestamp'))->toDateTimeString());
    });
})->with([
    'Send',
    'Reject',
    'RenderingFailure',
]);

it('keeps the milliseconds of the SES event timestamp', function () use ($sesNotificationFor, $dispatchSesNotification): void {
    $tenant = Tenant::query()->firstOrFail();

    $emailMessage = $tenant->execute(fn () => EmailMessage::factory()->create());

    $dispatchSesNotification($sesNotificationFor($tenant, $emailMessage, 'Delivery'));

    $tenant->execute(function () use ($emailMessage): void {
        expect($emailMessage->events()->sole()->occurred_at->format('Y-m-d H:i:s.v'))->toBe('2016-10-19 23:21:04.133');
    });
});

it('records repeated deliveries of the same SNS message once', function () use ($sesNotificationFor, $dispatchSesNotification): void {
    $tenant = Tenant::query()->firstOrFail();

    $emailMessage = $tenant->execute(fn () => EmailMessage::factory()->create());

    $snsData = $sesNotificationFor($tenant, $emailMessage, 'Bounce', 'sns-message');

    $dispatchSesNotification($snsData);
    $dispatchSesNotification($snsData);

    $tenant->execute(function () use ($emailMessage): void {
        expect($emailMessage->events()->sole()->sns_message_id)->toBe('sns-message');
    });
});

it('records a repeated delivery once when it reaches the listener', function () use ($sesNotificationFor): void {
    $tenant = Tenant::query()->firstOrFail();

    $tenant->execute(function () use ($tenant, $sesNotificationFor): void {
        $emailMessage = EmailMessage::factory()->create();

        $data = SesEventData::createFromSnsEnvelope($sesNotificationFor($tenant, $emailMessage, 'Bounce'));

        app(HandleSesBounceEvent::class)->handle(new SesBounceEvent($data, 'sns-message'));
        app(HandleSesBounceEvent::class)->handle(new SesBounceEvent($data, 'sns-message'));

        expect($emailMessage->events()->count())->toBe(1);
    });
});
