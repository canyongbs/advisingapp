<?php

use Twilio\Rest\Client;
use Tests\Unit\ClientMock;
use Twilio\Rest\Api\V2010;
use Twilio\Rest\MessagingBase;
use Twilio\Rest\Api\V2010\Account\MessageContext;
use Twilio\Rest\Api\V2010\Account\MessageInstance;
use AdvisingApp\Notification\Models\OutboundDeliverable;
use AdvisingApp\IntegrationTwilio\Settings\TwilioSettings;
use AdvisingApp\Notification\Enums\NotificationDeliveryStatus;
use AdvisingApp\IntegrationTwilio\Jobs\CheckSmsOutboundDeliverableStatus;

it('will update the status of an outbound deliverable accordingly', function (string $externalStatus) {
    $settings = app()->make(TwilioSettings::class);

    $settings->account_sid = 'abc123';
    $settings->auth_token = 'abc123';
    $settings->from_number = '+11231231234';

    $settings->save();

    // Given that we have an outbound deliverable with a non terminal status
    $outboundDeliverable = OutboundDeliverable::factory()->create([
        'channel' => 'sms',
        'external_status' => 'queued',
        'external_reference_id' => 'abc123',
    ]);

    $originalStatus = $outboundDeliverable->delivery_status;

    $clientMock = mock(ClientMock::class)
        ->shouldAllowMockingProtectedMethods();

    $mockMessageContext = mock(MessageContext::class);

    $clientMock->shouldReceive('messages')
        ->with('abc123')
        ->andReturn($mockMessageContext);

    $mockMessageContext->shouldReceive('fetch')->andReturn(
        new MessageInstance(
            new V2010(new MessagingBase(new Client())),
            [
                'sid' => 'abc123',
                'status' => $externalStatus,
                'from' => '+11231231234',
                'to' => '+11231231234',
                'body' => 'test',
                'num_segments' => 1,
            ],
            'abc123'
        )
    );

    app()->bind(Client::class, fn () => $clientMock);

    // And we reach out to Twilio to check on the status of the message because we may have missed a webhook
    CheckSmsOutboundDeliverableStatus::dispatchSync($outboundDeliverable);

    $outboundDeliverable->refresh();

    // Our delivery status should be updated based on the status we received from Twilio
    if ($externalStatus === 'delivered') {
        expect($outboundDeliverable->delivery_status)->toBe(NotificationDeliveryStatus::Successful);
    } elseif ($externalStatus === 'undelivered' || $externalStatus === 'failed') {
        expect($outboundDeliverable->delivery_status)->toBe(NotificationDeliveryStatus::Failed);
    } else {
        expect($outboundDeliverable->delivery_status)->toBe($originalStatus);
    }
})->with([
    'queued',
    'sent',
    'delivered',
    'undelivered',
    'failed',
]);
