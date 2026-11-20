<?php

use App\Support\ScrubSensitiveRequestData;
use Sentry\Event;

it('scrubs the password value from secret storage events', function (Event $event): void {
    $event->setRequest([
        'url' => 'https://example.test/widgets/forms/store-secret',
        'data' => ['value' => 'hunter2', 'previous_secret_id' => 'abc'],
    ]);

    $scrubbed = ScrubSensitiveRequestData::handle($event);

    assert($scrubbed instanceof Event);

    expect($scrubbed->getRequest()['data'])->toBe(['value' => '[Filtered]', 'previous_secret_id' => 'abc']);
})->with([
    'error' => fn (): Event => Event::createEvent(),
    'transaction' => fn (): Event => Event::createTransaction(),
]);

it('does not change unrelated events', function (): void {
    $event = Event::createEvent();
    $event->setRequest(['url' => 'https://example.test/forms', 'data' => ['value' => 'ordinary']]);

    $scrubbed = ScrubSensitiveRequestData::handle($event);

    assert($scrubbed instanceof Event);

    expect($scrubbed->getRequest()['data'])->toBe(['value' => 'ordinary']);
});

it('drops secret reveal events', function (): void {
    $event = Event::createEvent();
    $event->setRequest(['url' => 'https://example.test/submission-secrets/reveal', 'data' => ['secret_id' => 'x']]);

    expect(ScrubSensitiveRequestData::handle($event))->toBeNull();
});
