<?php

use function Pest\Laravel\post;
use function Pest\Laravel\withHeaders;
use function Tests\loadFixtureFromModule;

it('will abort the request if the request does not have the necessary header', function () {
    $response = post(
        route('inbound.webhook.twilio', 'status_update'),
        loadFixtureFromModule('integration-twilio', 'StatusCallback/sent'),
    );

    $response->assertNotFound();
});

it('will abort the request if the request cannot be verified to have originated from Twilio', function () {
    $response = withHeaders([
        'x-twilio-signature' => 'Not a legit signature',
    ])->post(
        route('inbound.webhook.twilio', 'status_update'),
        loadFixtureFromModule('integration-twilio', 'StatusCallback/sent'),
    );

    $response->assertNotFound();
});
