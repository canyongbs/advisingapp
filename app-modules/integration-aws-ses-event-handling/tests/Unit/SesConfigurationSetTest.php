<?php

use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Event;
use Illuminate\Mail\Events\MessageSent;

it('sets the X-SES-CONFIGURATION-SET header if mail.mailers.ses.configuration_set is set', function () {
    Event::fake(MessageSent::class);

    $configurationSet = 'test';

    config(['mail.mailers.ses.configuration_set' => $configurationSet]);

    Mail::raw(
        'Hello, welcome to Laravel!',
        fn ($message) => $message->to('test@test.com')->subject('Test')
    );

    Event::assertDispatched(
        fn (MessageSent $event) => $event->message->getHeaders()->get('X-SES-CONFIGURATION-SET')->getBody() === $configurationSet
    );
});

it('X-SES-CONFIGURATION-SET is not present if mail.mailers.ses.configuration_set is not', function () {
    Event::fake(MessageSent::class);

    config(['mail.mailers.ses.configuration_set' => null]);

    Mail::raw(
        'Hello, welcome to Laravel!',
        fn ($message) => $message->to('test@test.com')->subject('Test')
    );

    Event::assertDispatched(
        fn (MessageSent $event) => is_null($event->message->getHeaders()->get('X-SES-CONFIGURATION-SET'))
    );
});
