<?php

use Illuminate\Http\Request;

use function Tests\replaceKeyInFixture;
use function Tests\loadFixtureFromModule;

use AdvisingApp\Engagement\Models\EngagementDeliverable;
use AdvisingApp\Notification\Models\OutboundDeliverable;
use AdvisingApp\IntegrationTwilio\Actions\StatusCallback;
use AdvisingApp\Engagement\Enums\EngagementDeliveryStatus;
use AdvisingApp\Notification\Enums\NotificationDeliveryStatus;
use AdvisingApp\IntegrationTwilio\DataTransferObjects\TwilioStatusCallbackData;

test('it will appropriately update the status of an outbound deliverable based on the payload received', function () {
    // Given that we have an outbound deliverable
    $outboundDeliverable = OutboundDeliverable::factory()
        ->smsChannel()
        ->create([
            'external_reference_id' => '12345',
        ]);

    ray('outboundDeliverable', $outboundDeliverable);
    expect($outboundDeliverable->delivery_status)->toBe(NotificationDeliveryStatus::Awaiting);

    $payload = replaceKeyInFixture(
        fixture: loadFixtureFromModule('integration-twilio', 'StatusCallback/delivered'),
        key: 'MessageSid',
        value: $outboundDeliverable->external_reference_id,
    );

    // When we process the status callback webhook
    $request = Request::create('/', 'POST', $payload);
    $statusCallback = new StatusCallback(TwilioStatusCallbackData::fromRequest($request));
    $statusCallback->handle();

    $outboundDeliverable->refresh();

    // Our outbound deliverable should have been updated appropriately based on the status of the callback
    expect($outboundDeliverable->delivery_status)->toBe(NotificationDeliveryStatus::Successful);
});

test('it will update a related entity if one exists', function () {
    // Given that we have an outbound deliverable with a related EngagementDeliverable
    $engagementDeliverable = EngagementDeliverable::factory()
        ->sms()
        ->create();

    $outboundDeliverable = OutboundDeliverable::factory()
        ->smsChannel()
        ->create([
            'related_id' => $engagementDeliverable->id,
            'related_type' => $engagementDeliverable->getMorphClass(),
            'external_reference_id' => '12345',
        ]);

    expect($outboundDeliverable->delivery_status)->toBe(NotificationDeliveryStatus::Awaiting);
    expect($engagementDeliverable->delivery_status)->toBe(EngagementDeliveryStatus::Awaiting);

    $payload = replaceKeyInFixture(
        fixture: loadFixtureFromModule('integration-twilio', 'StatusCallback/delivered'),
        key: 'MessageSid',
        value: $outboundDeliverable->external_reference_id,
    );

    // When we process the status callback webhook
    $request = Request::create('/', 'POST', $payload);
    $statusCallback = new StatusCallback(TwilioStatusCallbackData::fromRequest($request));
    $statusCallback->handle();

    $outboundDeliverable->refresh();
    $engagementDeliverable->refresh();

    // Our outbound deliverable, along with our engagement deliverable
    // should have been updated appropriately based on the status of the callback
    expect($outboundDeliverable->delivery_status)->toBe(NotificationDeliveryStatus::Successful);
    expect($engagementDeliverable->delivery_status)->toBe(EngagementDeliveryStatus::Successful);
});
