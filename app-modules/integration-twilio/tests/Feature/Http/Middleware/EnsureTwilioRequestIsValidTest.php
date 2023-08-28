<?php

it('will abort the request if the request does not have the necessary header', function () {
    $response = $this->post(
        route('inbound.webhook.twilio', 'status-update'),
        // And the request does not have a valid secret
        $this->loadFixtureFromModule('webhook', 'Twilio/StatusEvents/sent'),
    );

    $response->assertNotFound();
});

it('will abort the request if the request cannot be verified to have originated from Twilio', function () {
    $response = $this->withHeaders([
        'HTTP_X_TWILIO_SIGNATURE' => 'Not a legit signature',
    ])->post(
        route('inbound.webhook.twilio', 'status-update'),
        $this->loadFixtureFromModule('webhook', 'Twilio/StatusEvents/sent'),
    );

    $response->assertNotFound();
});
