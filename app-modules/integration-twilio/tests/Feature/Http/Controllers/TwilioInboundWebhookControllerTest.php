<?php

use function Pest\Laravel\post;

use Illuminate\Support\Facades\Queue;

use function Tests\loadFixtureFromModule;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\withoutMiddleware;
use function Pest\Laravel\assertDatabaseMissing;

use Assist\IntegrationTwilio\Actions\MessageReceived;

it('will create an inbound webhook with the Twilio source and the correct event', function () {
    Queue::fake([MessageReceived::class]);
    withoutMiddleware();

    $response = post(
        route('inbound.webhook.twilio', 'message_received'),
        loadFixtureFromModule('integration-twilio', 'MessageReceived/payload'),
    );

    $response->assertOk();

    assertDatabaseHas('inbound_webhooks', [
        'source' => 'twilio',
        'event' => 'message_received',
    ]);

    assertDatabaseMissing('inbound_webhooks', [
        'source' => 'twilio',
        'event' => 'status_update',
    ]);

    post(
        route('inbound.webhook.twilio', 'status_update'),
        loadFixtureFromModule('integration-twilio', 'StatusCallback/sent'),
    );

    assertDatabaseHas('inbound_webhooks', [
        'source' => 'twilio',
        'event' => 'status_update',
    ]);
});
