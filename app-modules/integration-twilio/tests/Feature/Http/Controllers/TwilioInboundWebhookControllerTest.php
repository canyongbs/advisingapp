<?php

it('will create an inbound webhook with the Twilio source and the correct event', function () {
    $this->withoutMiddleware();

    $response = $this->post(
        route('inbound.webhook.twilio', 'message_received'),
        // TODO Change this payload for this specific test...
        $this->loadFixtureFromModule('integration-twilio', 'StatusCallback/sent'),
    );

    $response->assertOk();

    $this->assertDatabaseHas('inbound_webhooks', [
        'source' => 'twilio',
        'event' => 'message_received',
    ]);

    $this->assertDatabaseDoesntHave('inbound_webhooks', [
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
