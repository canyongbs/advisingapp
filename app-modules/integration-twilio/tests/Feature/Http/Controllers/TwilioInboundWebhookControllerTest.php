<?php

use Illuminate\Support\Facades\Queue;
use Assist\IntegrationTwilio\Actions\MessageReceived;

it('will create an inbound webhook with the Twilio source and the correct event', function () {
    Queue::fake([MessageReceived::class]);
    $this->withoutMiddleware();

    $response = $this->post(
        route('inbound.webhook.twilio', 'message_received'),
        $this->loadFixtureFromModule('integration-twilio', 'MessageReceived/payload'),
    );

    $response->assertOk();

    $this->assertDatabaseHas('inbound_webhooks', [
        'source' => 'twilio',
        'event' => 'message_received',
    ]);

    $this->assertDatabaseMissing('inbound_webhooks', [
        'source' => 'twilio',
        'event' => 'status_update',
    ]);

    $response = $this->post(
        route('inbound.webhook.twilio', 'status_update'),
        $this->loadFixtureFromModule('integration-twilio', 'StatusCallback/sent'),
    );

    $this->assertDatabaseHas('inbound_webhooks', [
        'source' => 'twilio',
        'event' => 'status_update',
    ]);
});
