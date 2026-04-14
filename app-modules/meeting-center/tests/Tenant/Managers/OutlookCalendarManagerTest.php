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

use AdvisingApp\MeetingCenter\Enums\CalendarProvider;
use AdvisingApp\MeetingCenter\Exceptions\CouldNotRefreshToken;
use AdvisingApp\MeetingCenter\Exceptions\MicrosoftGraphRateLimited;
use AdvisingApp\MeetingCenter\Managers\OutlookCalendarManager;
use AdvisingApp\MeetingCenter\Models\Calendar;
use AdvisingApp\MeetingCenter\Models\CalendarEvent;
use AdvisingApp\MeetingCenter\Notifications\CalendarRequiresReconnectNotification;
use AdvisingApp\MeetingCenter\Settings\AzureCalendarSettings;
use App\Models\User;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ServerException;
use GuzzleHttp\Psr7\Request as GuzzleRequest;
use GuzzleHttp\Psr7\Response as GuzzleResponse;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Notification;
use Microsoft\Graph\Graph;
use Microsoft\Graph\Http\GraphCollectionRequest;
use Microsoft\Graph\Http\GraphRequest;
use Microsoft\Graph\Http\GraphResponse;
use Microsoft\Graph\Model\DateTimeTimeZone;
use Microsoft\Graph\Model\Event;
use Mockery\MockInterface;

function createMockOutlookEvent(string $id, string $subject, ?string $iCalUid = null): Event // @phpstan-ignore MeliorStan.parameterNameNotCamelCase
{
    $start = new DateTimeTimeZone();
    $start->setDateTime('2026-03-05T10:00:00');
    $start->setTimeZone('UTC');

    $end = new DateTimeTimeZone();
    $end->setDateTime('2026-03-05T11:00:00');
    $end->setTimeZone('UTC');

    $event = new Event();
    $event->setId($id);
    $event->setICalUId($iCalUid ?? "ical-{$id}");
    $event->setSubject($subject);
    $event->setBodyPreview('Test body');
    $event->setStart($start);
    $event->setEnd($end);
    $event->setAttendees([]);

    return $event;
}

/** @param array<string, mixed> $overrides */
function createOutlookCalendar(array $overrides = []): Calendar
{
    return Calendar::factory()
        ->for(User::factory())
        ->create(array_merge([
            'provider_type' => CalendarProvider::Outlook,
            'provider_id' => 'test-calendar-id',
            'provider_email' => 'test@example.com',
            'oauth_token' => 'test-token',
            'oauth_refresh_token' => 'test-refresh-token',
            'oauth_token_expires_at' => now()->addHour(),
        ], $overrides));
}

/** @return array{0: MockInterface&OutlookCalendarManager, 1: MockInterface&Graph} */
function createMockedManager(): array
{
    $graph = Mockery::mock(Graph::class);
    $graph->shouldReceive('setAccessToken')->andReturnSelf(); // @phpstan-ignore method.notFound

    $manager = Mockery::mock(OutlookCalendarManager::class)->makePartial();
    $manager->shouldReceive('makeClient')->andReturn($graph);

    return [$manager, $graph]; // @phpstan-ignore return.type
}

function mockAzureCalendarSettings(): void
{
    $settings = Mockery::mock(AzureCalendarSettings::class);
    $settings->client_id = 'test-client-id'; // @phpstan-ignore property.notFound
    $settings->client_secret = 'test-client-secret'; // @phpstan-ignore property.notFound
    $settings->tenant_id = 'test-tenant-id'; // @phpstan-ignore property.notFound
    app()->instance(AzureCalendarSettings::class, $settings);
}

it('correctly normalizes the Outlook API event response into an array', function (mixed $apiResponse, int $expectedCount) {
    /** @var MockInterface&GraphResponse $response */
    $response = Mockery::mock(GraphResponse::class);
    /** @phpstan-ignore-next-line */
    $response->shouldReceive('getResponseAsObject')->with(Event::class)->andReturn($apiResponse);
    $response->shouldReceive('getNextLink')
        ->andReturn(null);

    /** @var MockInterface&GraphCollectionRequest $request */
    $request = Mockery::mock(GraphCollectionRequest::class);
    $request->shouldReceive('execute')
        ->andReturn($response);

    /** @var MockInterface&Graph $graph */
    $graph = Mockery::mock(Graph::class);
    /** @phpstan-ignore-next-line */
    $graph->shouldReceive('setAccessToken')->andReturnSelf();
    $graph->shouldReceive('createCollectionRequest')
        ->andReturn($request);

    $calendar = createOutlookCalendar();

    /** @var MockInterface&OutlookCalendarManager $manager */
    $manager = Mockery::mock(OutlookCalendarManager::class)->makePartial();
    $manager->shouldReceive('makeClient')
        ->andReturn($graph);

    $events = $manager->getEvents($calendar);

    expect($events)
        ->toBeArray()
        ->toHaveCount($expectedCount)
        ->each
        ->toBeInstanceOf(Event::class);
})
    ->with([
        'API returns a single Event object instead of an array' => fn () => [
            createMockOutlookEvent('event-1', 'Single Event'),
            1,
        ],
        'API returns an array of multiple Event objects' => fn () => [
            [
                createMockOutlookEvent('event-1', 'First Event'),
                createMockOutlookEvent('event-2', 'Second Event'),
            ],
            2,
        ],
        'API returns an empty array when no events exist' => fn () => [
            [],
            0,
        ],
    ]);

// ──────────────────────────────────────────────────
// getEvents – paginated responses
// ──────────────────────────────────────────────────

it('handles paginated getEvents responses via getNextLink', function () {
    $page1Response = Mockery::mock(GraphResponse::class);
    $page1Response->shouldReceive('getResponseAsObject') // @phpstan-ignore method.notFound
        ->with(Event::class)
        ->andReturn([createMockOutlookEvent('e1', 'Event 1')]);
    $page1Response->shouldReceive('getNextLink')
        ->andReturn('https://graph.microsoft.com/v1.0/me/calendar/calendarView?$skiptoken=abc');

    $page2Response = Mockery::mock(GraphResponse::class);
    $page2Response->shouldReceive('getResponseAsObject') // @phpstan-ignore method.notFound
        ->with(Event::class)
        ->andReturn([createMockOutlookEvent('e2', 'Event 2')]);
    $page2Response->shouldReceive('getNextLink')
        ->andReturn(null);

    $page1Request = Mockery::mock(GraphCollectionRequest::class);
    $page1Request->shouldReceive('execute')->andReturn($page1Response);

    $page2Request = Mockery::mock(GraphCollectionRequest::class);
    $page2Request->shouldReceive('execute')->andReturn($page2Response);

    $graph = Mockery::mock(Graph::class);
    $graph->shouldReceive('setAccessToken')->andReturnSelf(); // @phpstan-ignore method.notFound
    $graph->shouldReceive('createCollectionRequest')
        ->andReturn($page1Request, $page2Request);

    $manager = Mockery::mock(OutlookCalendarManager::class)->makePartial();
    $manager->shouldReceive('makeClient')->andReturn($graph);

    $events = $manager->getEvents(createOutlookCalendar()); // @phpstan-ignore method.notFound

    expect($events)->toHaveCount(2); // @phpstan-ignore argument.templateType
});

// ──────────────────────────────────────────────────
// getEvents – error handling
// ──────────────────────────────────────────────────

it('catches ClientException 401 in getEvents and refreshes token then retries', function () {
    $guzzleRequest = new GuzzleRequest('GET', 'https://graph.microsoft.com/v1.0/me/calendar');
    $guzzleResponse = new GuzzleResponse(401);
    $clientException = new ClientException('Unauthorized', $guzzleRequest, $guzzleResponse);

    $successResponse = Mockery::mock(GraphResponse::class);
    $successResponse->shouldReceive('getResponseAsObject') // @phpstan-ignore method.notFound
        ->with(Event::class)
        ->andReturn([createMockOutlookEvent('e1', 'Event 1')]);
    $successResponse->shouldReceive('getNextLink')->andReturn(null);

    // executeWithRetry calls retry(times: 3) which attempts up to 3 times.
    // All 3 attempts fail with 401 → exception bubbles to catch block.
    // Catch block refreshes token, sets access token, then calls executeWithRetry again.
    // The retry wrapper in the second executeWithRetry call succeeds on first attempt.
    $callCount = 0;
    $request = Mockery::mock(GraphCollectionRequest::class);
    $request->shouldReceive('execute') // @phpstan-ignore method.notFound
        ->andReturnUsing(function () use (&$callCount, $clientException, $successResponse) {
            $callCount++;

            if ($callCount <= 3) {
                throw $clientException;
            }

            return $successResponse;
        });
    $request->shouldReceive('setAccessToken')->once(); // @phpstan-ignore method.notFound

    $graph = Mockery::mock(Graph::class);
    $graph->shouldReceive('setAccessToken')->andReturnSelf(); // @phpstan-ignore method.notFound
    $graph->shouldReceive('createCollectionRequest')
        ->andReturn($request);

    $calendar = createOutlookCalendar();

    $manager = Mockery::mock(OutlookCalendarManager::class)->makePartial();
    $manager->shouldReceive('makeClient')->andReturn($graph);
    $manager->shouldReceive('refreshToken')->once()->andReturn($calendar); // @phpstan-ignore method.notFound

    $events = $manager->getEvents($calendar); // @phpstan-ignore method.notFound

    expect($events)->toHaveCount(1); // @phpstan-ignore argument.templateType
});

it('catches ClientException with Retry-After in getEvents and throws MicrosoftGraphRateLimited', function () {
    $guzzleRequest = new GuzzleRequest('GET', 'https://graph.microsoft.com/v1.0/me/calendar');
    $guzzleResponse = new GuzzleResponse(429, ['Retry-After' => '60']);
    $clientException = new ClientException('Too Many Requests', $guzzleRequest, $guzzleResponse);

    $request = Mockery::mock(GraphCollectionRequest::class);
    $request->shouldReceive('execute')->andThrow($clientException); // @phpstan-ignore method.notFound

    $graph = Mockery::mock(Graph::class);
    $graph->shouldReceive('setAccessToken')->andReturnSelf(); // @phpstan-ignore method.notFound
    $graph->shouldReceive('createCollectionRequest')->andReturn($request);

    $manager = Mockery::mock(OutlookCalendarManager::class)->makePartial();
    $manager->shouldReceive('makeClient')->andReturn($graph);

    expect(fn () => $manager->getEvents(createOutlookCalendar())) // @phpstan-ignore method.notFound, argument.unresolvableType, function.unresolvableReturnType
        ->toThrow(function (MicrosoftGraphRateLimited $exception) { // @phpstan-ignore argument.type
            expect($exception->retryAfterSeconds)->toBe(60);
        });
});

it('rethrows non-401 ClientException without Retry-After in getEvents', function () {
    $guzzleRequest = new GuzzleRequest('GET', 'https://graph.microsoft.com/v1.0/me/calendar');
    $guzzleResponse = new GuzzleResponse(403);
    $clientException = new ClientException('Forbidden', $guzzleRequest, $guzzleResponse);

    $request = Mockery::mock(GraphCollectionRequest::class);
    $request->shouldReceive('execute')->andThrow($clientException); // @phpstan-ignore method.notFound

    $graph = Mockery::mock(Graph::class);
    $graph->shouldReceive('setAccessToken')->andReturnSelf(); // @phpstan-ignore method.notFound
    $graph->shouldReceive('createCollectionRequest')->andReturn($request);

    $manager = Mockery::mock(OutlookCalendarManager::class)->makePartial();
    $manager->shouldReceive('makeClient')->andReturn($graph);

    expect(fn () => $manager->getEvents(createOutlookCalendar())) // @phpstan-ignore method.notFound, argument.unresolvableType, function.unresolvableReturnType
        ->toThrow(ClientException::class);
});

it('catches ServerException in getEvents with Retry-After and throws MicrosoftGraphRateLimited', function () {
    $guzzleRequest = new GuzzleRequest('GET', 'https://graph.microsoft.com/v1.0/me/calendar');
    $guzzleResponse = new GuzzleResponse(500, ['Retry-After' => '45']);
    $serverException = new ServerException('Server Error', $guzzleRequest, $guzzleResponse);

    $request = Mockery::mock(GraphCollectionRequest::class);
    $request->shouldReceive('execute')->andThrow($serverException); // @phpstan-ignore method.notFound

    $graph = Mockery::mock(Graph::class);
    $graph->shouldReceive('setAccessToken')->andReturnSelf(); // @phpstan-ignore method.notFound
    $graph->shouldReceive('createCollectionRequest')->andReturn($request);

    $manager = Mockery::mock(OutlookCalendarManager::class)->makePartial();
    $manager->shouldReceive('makeClient')->andReturn($graph);

    expect(fn () => $manager->getEvents(createOutlookCalendar())) // @phpstan-ignore method.notFound, argument.unresolvableType, function.unresolvableReturnType
        ->toThrow(function (MicrosoftGraphRateLimited $exception) { // @phpstan-ignore argument.type
            expect($exception->retryAfterSeconds)->toBe(45);
        });
});

it('catches ServerException in getEvents without Retry-After and defaults to 30 seconds', function () {
    $guzzleRequest = new GuzzleRequest('GET', 'https://graph.microsoft.com/v1.0/me/calendar');
    $guzzleResponse = new GuzzleResponse(500);
    $serverException = new ServerException('Server Error', $guzzleRequest, $guzzleResponse);

    $request = Mockery::mock(GraphCollectionRequest::class);
    $request->shouldReceive('execute')->andThrow($serverException); // @phpstan-ignore method.notFound

    $graph = Mockery::mock(Graph::class);
    $graph->shouldReceive('setAccessToken')->andReturnSelf(); // @phpstan-ignore method.notFound
    $graph->shouldReceive('createCollectionRequest')->andReturn($request);

    $manager = Mockery::mock(OutlookCalendarManager::class)->makePartial();
    $manager->shouldReceive('makeClient')->andReturn($graph);

    expect(fn () => $manager->getEvents(createOutlookCalendar())) // @phpstan-ignore method.notFound, argument.unresolvableType, function.unresolvableReturnType
        ->toThrow(function (MicrosoftGraphRateLimited $exception) { // @phpstan-ignore argument.type
            expect($exception->retryAfterSeconds)->toBe(30);
        });
});

// ──────────────────────────────────────────────────
// createEvent
// ──────────────────────────────────────────────────

it('creates an event and saves provider_id and provider_uid', function () {
    CalendarEvent::unsetEventDispatcher();

    $providerEvent = createMockOutlookEvent('provider-id-123', 'Created Event', 'ical-uid-123');

    $response = Mockery::mock(GraphResponse::class);
    $response->shouldReceive('getResponseAsObject')->with(Event::class)->andReturn($providerEvent); // @phpstan-ignore method.notFound

    $graphRequest = Mockery::mock(GraphRequest::class);
    $graphRequest->shouldReceive('attachBody')->andReturnSelf(); // @phpstan-ignore method.notFound
    $graphRequest->shouldReceive('execute')->andReturn($response);

    $graph = Mockery::mock(Graph::class);
    $graph->shouldReceive('setAccessToken')->andReturnSelf(); // @phpstan-ignore method.notFound
    $graph->shouldReceive('createRequest')->andReturn($graphRequest);

    $calendar = createOutlookCalendar();
    $event = CalendarEvent::factory()->for($calendar)->create();

    $manager = Mockery::mock(OutlookCalendarManager::class)->makePartial();
    $manager->shouldReceive('makeClient')->andReturn($graph);

    $manager->createEvent($event); // @phpstan-ignore method.notFound

    $event->refresh();
    expect($event->provider_id)->toBe('provider-id-123')
        ->and($event->provider_uid)->toBe('ical-uid-123');
});

it('catches ServerException in createEvent and throws MicrosoftGraphRateLimited', function () {
    CalendarEvent::unsetEventDispatcher();

    $guzzleRequest = new GuzzleRequest('POST', 'https://graph.microsoft.com/v1.0/me/calendars/x/events');
    $guzzleResponse = new GuzzleResponse(503);
    $serverException = new ServerException('Service Unavailable', $guzzleRequest, $guzzleResponse);

    $graphRequest = Mockery::mock(GraphRequest::class);
    $graphRequest->shouldReceive('attachBody')->andReturnSelf(); // @phpstan-ignore method.notFound
    $graphRequest->shouldReceive('execute')->andThrow($serverException); // @phpstan-ignore method.notFound

    $graph = Mockery::mock(Graph::class);
    $graph->shouldReceive('setAccessToken')->andReturnSelf(); // @phpstan-ignore method.notFound
    $graph->shouldReceive('createRequest')->andReturn($graphRequest);

    $calendar = createOutlookCalendar();
    $event = CalendarEvent::factory()->for($calendar)->create();

    $manager = Mockery::mock(OutlookCalendarManager::class)->makePartial();
    $manager->shouldReceive('makeClient')->andReturn($graph);

    expect(fn () => $manager->createEvent($event)) // @phpstan-ignore method.notFound, argument.unresolvableType, function.unresolvableReturnType
        ->toThrow(MicrosoftGraphRateLimited::class);
});

// ──────────────────────────────────────────────────
// updateEvent
// ──────────────────────────────────────────────────

it('updates an event and saves provider_id and provider_uid', function () {
    CalendarEvent::unsetEventDispatcher();

    $providerEvent = createMockOutlookEvent('provider-id-456', 'Updated Event', 'ical-uid-456');

    $response = Mockery::mock(GraphResponse::class);
    $response->shouldReceive('getResponseAsObject')->with(Event::class)->andReturn($providerEvent); // @phpstan-ignore method.notFound

    $graphRequest = Mockery::mock(GraphRequest::class);
    $graphRequest->shouldReceive('attachBody')->andReturnSelf(); // @phpstan-ignore method.notFound
    $graphRequest->shouldReceive('execute')->andReturn($response);

    $graph = Mockery::mock(Graph::class);
    $graph->shouldReceive('setAccessToken')->andReturnSelf(); // @phpstan-ignore method.notFound
    $graph->shouldReceive('createRequest')->andReturn($graphRequest);

    $calendar = createOutlookCalendar();
    $event = CalendarEvent::factory()->for($calendar)->create([
        'provider_id' => 'old-provider-id',
    ]);

    $manager = Mockery::mock(OutlookCalendarManager::class)->makePartial();
    $manager->shouldReceive('makeClient')->andReturn($graph);

    $manager->updateEvent($event); // @phpstan-ignore method.notFound

    $event->refresh();
    expect($event->provider_id)->toBe('provider-id-456')
        ->and($event->provider_uid)->toBe('ical-uid-456');
});

it('catches ServerException in updateEvent and throws MicrosoftGraphRateLimited', function () {
    CalendarEvent::unsetEventDispatcher();

    $guzzleRequest = new GuzzleRequest('PATCH', 'https://graph.microsoft.com/v1.0/me/calendars/x/events/y');
    $guzzleResponse = new GuzzleResponse(500);
    $serverException = new ServerException('Server Error', $guzzleRequest, $guzzleResponse);

    $graphRequest = Mockery::mock(GraphRequest::class);
    $graphRequest->shouldReceive('attachBody')->andReturnSelf(); // @phpstan-ignore method.notFound
    $graphRequest->shouldReceive('execute')->andThrow($serverException); // @phpstan-ignore method.notFound

    $graph = Mockery::mock(Graph::class);
    $graph->shouldReceive('setAccessToken')->andReturnSelf(); // @phpstan-ignore method.notFound
    $graph->shouldReceive('createRequest')->andReturn($graphRequest);

    $calendar = createOutlookCalendar();
    $event = CalendarEvent::factory()->for($calendar)->create([
        'provider_id' => 'existing-id',
    ]);

    $manager = Mockery::mock(OutlookCalendarManager::class)->makePartial();
    $manager->shouldReceive('makeClient')->andReturn($graph);

    expect(fn () => $manager->updateEvent($event)) // @phpstan-ignore method.notFound, argument.unresolvableType, function.unresolvableReturnType
        ->toThrow(MicrosoftGraphRateLimited::class);
});

// ──────────────────────────────────────────────────
// deleteEvent
// ──────────────────────────────────────────────────

it('deletes an event successfully', function () {
    CalendarEvent::unsetEventDispatcher();

    $graphRequest = Mockery::mock(GraphRequest::class);
    $graphRequest->shouldReceive('execute')->once()->andReturn(null); // @phpstan-ignore method.notFound

    $graph = Mockery::mock(Graph::class);
    $graph->shouldReceive('setAccessToken')->andReturnSelf(); // @phpstan-ignore method.notFound
    $graph->shouldReceive('createRequest')->andReturn($graphRequest);

    $calendar = createOutlookCalendar();
    $event = CalendarEvent::factory()->for($calendar)->create([
        'provider_id' => 'event-to-delete',
    ]);

    $manager = Mockery::mock(OutlookCalendarManager::class)->makePartial();
    $manager->shouldReceive('makeClient')->andReturn($graph);

    $manager->deleteEvent($event); // @phpstan-ignore method.notFound

    // No exception means success
    expect(true)->toBeTrue();
});

it('catches ServerException in deleteEvent and throws MicrosoftGraphRateLimited', function () {
    CalendarEvent::unsetEventDispatcher();

    $guzzleRequest = new GuzzleRequest('DELETE', 'https://graph.microsoft.com/v1.0/me/calendars/x/events/y');
    $guzzleResponse = new GuzzleResponse(502);
    $serverException = new ServerException('Bad Gateway', $guzzleRequest, $guzzleResponse);

    $graphRequest = Mockery::mock(GraphRequest::class);
    $graphRequest->shouldReceive('execute')->andThrow($serverException); // @phpstan-ignore method.notFound

    $graph = Mockery::mock(Graph::class);
    $graph->shouldReceive('setAccessToken')->andReturnSelf(); // @phpstan-ignore method.notFound
    $graph->shouldReceive('createRequest')->andReturn($graphRequest);

    $calendar = createOutlookCalendar();
    $event = CalendarEvent::factory()->for($calendar)->create([
        'provider_id' => 'event-to-delete',
    ]);

    $manager = Mockery::mock(OutlookCalendarManager::class)->makePartial();
    $manager->shouldReceive('makeClient')->andReturn($graph);

    expect(fn () => $manager->deleteEvent($event)) // @phpstan-ignore method.notFound, argument.unresolvableType, function.unresolvableReturnType
        ->toThrow(MicrosoftGraphRateLimited::class);
});

// ──────────────────────────────────────────────────
// syncEvents
// ──────────────────────────────────────────────────

it('creates new local events from provider events during sync', function () {
    $providerEvents = [
        createMockOutlookEvent('p1', 'Provider Event 1'),
        createMockOutlookEvent('p2', 'Provider Event 2'),
    ];

    $calendar = createOutlookCalendar();

    $manager = Mockery::mock(OutlookCalendarManager::class)->makePartial();
    $manager->shouldReceive('getEvents')->andReturn($providerEvents);
    $manager->shouldReceive('createEvent')->never(); // @phpstan-ignore method.notFound

    $start = now()->subMonth()->startOfDay();
    $end = now()->addMonth()->endOfDay();

    $manager->syncEvents($calendar, new DateTime($start->toDateTimeString()), new DateTime($end->toDateTimeString())); // @phpstan-ignore method.notFound

    expect($calendar->events()->count())->toBe(2)
        ->and($calendar->events()->where('provider_id', 'p1')->exists())->toBeTrue()
        ->and($calendar->events()->where('provider_id', 'p2')->exists())->toBeTrue();
});

it('updates existing local events when provider data changes during sync', function () {
    CalendarEvent::unsetEventDispatcher();

    $calendar = createOutlookCalendar();
    CalendarEvent::factory()->for($calendar)->create([
        'provider_id' => 'p1',
        'title' => 'Old Title',
    ]);

    $providerEvent = createMockOutlookEvent('p1', 'Updated Title');

    $manager = Mockery::mock(OutlookCalendarManager::class)->makePartial();
    $manager->shouldReceive('getEvents')->andReturn([$providerEvent]);
    $manager->shouldReceive('createEvent')->never(); // @phpstan-ignore method.notFound

    $start = now()->subMonth()->startOfDay();
    $end = now()->addMonth()->endOfDay();

    $manager->syncEvents($calendar, new DateTime($start->toDateTimeString()), new DateTime($end->toDateTimeString())); // @phpstan-ignore method.notFound

    expect($calendar->events()->count())->toBe(1)
        ->and($calendar->events()->first()->title)->toBe('Updated Title');
});

it('pushes local events without provider_id to provider during sync', function () {
    CalendarEvent::unsetEventDispatcher();

    $calendar = createOutlookCalendar();
    CalendarEvent::factory()->for($calendar)->create([
        'provider_id' => null,
        'title' => 'Local Only Event',
    ]);

    $manager = Mockery::mock(OutlookCalendarManager::class)->makePartial();
    $manager->shouldReceive('getEvents')->andReturn([]);
    $manager->shouldReceive('createEvent')->once(); // @phpstan-ignore method.notFound

    $start = now()->subMonth()->startOfDay();
    $end = now()->addMonth()->endOfDay();

    $manager->syncEvents($calendar, new DateTime($start->toDateTimeString()), new DateTime($end->toDateTimeString())); // @phpstan-ignore method.notFound
});

it('deletes orphaned local events within the synced date range', function () {
    CalendarEvent::unsetEventDispatcher();

    $calendar = createOutlookCalendar();
    CalendarEvent::factory()->for($calendar)->create([
        'provider_id' => 'orphaned-event',
        'starts_at' => now(),
    ]);

    $manager = Mockery::mock(OutlookCalendarManager::class)->makePartial();
    $manager->shouldReceive('getEvents')->andReturn([]);

    $start = now()->subMonth()->startOfDay();
    $end = now()->addMonth()->endOfDay();

    $manager->syncEvents($calendar, new DateTime($start->toDateTimeString()), new DateTime($end->toDateTimeString())); // @phpstan-ignore method.notFound

    expect($calendar->events()->count())->toBe(0);
});

it('does not delete orphaned events outside the synced date range', function () {
    CalendarEvent::unsetEventDispatcher();

    $calendar = createOutlookCalendar();
    CalendarEvent::factory()->for($calendar)->create([
        'provider_id' => 'future-event',
        'starts_at' => now()->addMonths(6),
    ]);

    $manager = Mockery::mock(OutlookCalendarManager::class)->makePartial();
    $manager->shouldReceive('getEvents')->andReturn([]);

    $start = now()->subMonth()->startOfDay();
    $end = now()->addMonth()->endOfDay();

    $manager->syncEvents($calendar, new DateTime($start->toDateTimeString()), new DateTime($end->toDateTimeString())); // @phpstan-ignore method.notFound

    expect($calendar->events()->count())->toBe(1);
});

// ──────────────────────────────────────────────────
// refreshToken
// ──────────────────────────────────────────────────

it('refreshes token and updates calendar fields', function () {
    mockAzureCalendarSettings();

    Http::fake([
        'https://login.microsoftonline.com/*' => Http::response([
            'access_token' => 'new-access-token',
            'refresh_token' => 'new-refresh-token',
            'expires_in' => 3600,
        ]),
    ]);

    $calendar = createOutlookCalendar();
    $manager = new OutlookCalendarManager();

    $result = $manager->refreshToken($calendar);

    expect($result->oauth_token)->toBe('new-access-token')
        ->and($result->oauth_refresh_token)->toBe('new-refresh-token')
        ->and($result->oauth_token_expires_at)->not->toBeNull();

    $calendar->refresh();
    expect($calendar->oauth_token)->toBe('new-access-token');
});

it('throws CouldNotRefreshToken when refresh token is blank', function () {
    $calendar = createOutlookCalendar(['oauth_refresh_token' => null]);
    $manager = new OutlookCalendarManager();

    expect(fn () => $manager->refreshToken($calendar))
        ->toThrow(CouldNotRefreshToken::class, 'No refresh token available for calendar.');
});

it('disconnects and notifies user on 401 response during token refresh', function () {
    Notification::fake();
    mockAzureCalendarSettings();

    Http::fake([
        'https://login.microsoftonline.com/*' => Http::response(['error' => 'unauthorized'], 401),
    ]);

    $calendar = createOutlookCalendar();
    $manager = new OutlookCalendarManager();

    expect(fn () => $manager->refreshToken($calendar))
        ->toThrow(CouldNotRefreshToken::class);

    $calendar->refresh();
    expect($calendar->oauth_token)->toBeNull()
        ->and($calendar->oauth_refresh_token)->toBeNull()
        ->and($calendar->oauth_token_expires_at)->toBeNull();

    Notification::assertSentTo($calendar->user, CalendarRequiresReconnectNotification::class);
});

it('disconnects and notifies on invalid_grant with AADSTS50173', function () {
    Notification::fake();
    mockAzureCalendarSettings();

    Http::fake([
        'https://login.microsoftonline.com/*' => Http::response([
            'error' => 'invalid_grant',
            'error_description' => 'AADSTS50173: The provided grant has expired due to it being revoked.',
        ], 400),
    ]);

    $calendar = createOutlookCalendar();
    $manager = new OutlookCalendarManager();

    expect(fn () => $manager->refreshToken($calendar))
        ->toThrow(CouldNotRefreshToken::class);

    $calendar->refresh();
    expect($calendar->oauth_token)->toBeNull()
        ->and($calendar->oauth_refresh_token)->toBeNull();

    Notification::assertSentTo($calendar->user, CalendarRequiresReconnectNotification::class);
});

it('disconnects and notifies on invalid_grant with AADSTS50057', function () {
    Notification::fake();
    mockAzureCalendarSettings();

    Http::fake([
        'https://login.microsoftonline.com/*' => Http::response([
            'error' => 'invalid_grant',
            'error_description' => 'AADSTS50057: The user account is disabled.',
        ], 400),
    ]);

    $calendar = createOutlookCalendar();
    $manager = new OutlookCalendarManager();

    expect(fn () => $manager->refreshToken($calendar))
        ->toThrow(CouldNotRefreshToken::class);

    $calendar->refresh();
    expect($calendar->oauth_token)->toBeNull()
        ->and($calendar->oauth_refresh_token)->toBeNull();

    Notification::assertSentTo($calendar->user, CalendarRequiresReconnectNotification::class);
});

it('throws RequestException on non-invalid_grant 400 errors during refresh', function () {
    mockAzureCalendarSettings();

    Http::fake([
        'https://login.microsoftonline.com/*' => Http::response([
            'error' => 'invalid_request',
            'error_description' => 'Some other error.',
        ], 400),
    ]);

    $calendar = createOutlookCalendar();
    $manager = new OutlookCalendarManager();

    expect(fn () => $manager->refreshToken($calendar))
        ->toThrow(RequestException::class);
});

it('skips notification if tokens already cleared by another process', function () {
    Notification::fake();
    mockAzureCalendarSettings();

    Http::fake([
        'https://login.microsoftonline.com/*' => Http::response([
            'error' => 'invalid_grant',
            'error_description' => 'AADSTS50173: The provided grant has expired.',
        ], 400),
    ]);

    $calendar = createOutlookCalendar();

    // Simulate another process clearing the tokens before disconnectAndNotify runs
    Calendar::withoutEvents(function () use ($calendar) {
        $calendar->newQuery()
            ->where('id', $calendar->id)
            ->update([
                'oauth_token' => null,
                'oauth_refresh_token' => null,
                'oauth_token_expires_at' => null,
            ]);
    });

    $manager = new OutlookCalendarManager();

    expect(fn () => $manager->refreshToken($calendar))
        ->toThrow(CouldNotRefreshToken::class);

    Notification::assertNothingSent();
});

// ──────────────────────────────────────────────────
// makeClient
// ──────────────────────────────────────────────────

it('returns Graph client when token is not expired', function () {
    $calendar = createOutlookCalendar([
        'oauth_token_expires_at' => now()->addHour(),
    ]);

    $manager = new OutlookCalendarManager();
    $client = $manager->makeClient($calendar);

    expect($client)->toBeInstanceOf(Graph::class);
});

it('refreshes token in makeClient when token is expired', function () {
    mockAzureCalendarSettings();

    Http::fake([
        'https://login.microsoftonline.com/*' => Http::response([
            'access_token' => 'refreshed-token',
            'refresh_token' => 'refreshed-refresh-token',
            'expires_in' => 3600,
        ]),
    ]);

    $calendar = createOutlookCalendar([
        'oauth_token_expires_at' => now()->subMinute(),
    ]);

    $manager = new OutlookCalendarManager();
    $client = $manager->makeClient($calendar);

    expect($client)->toBeInstanceOf(Graph::class);

    $calendar->refresh();
    expect($calendar->oauth_token)->toBe('refreshed-token');
});

it('refreshes token in makeClient when oauth_token_expires_at is null', function () {
    mockAzureCalendarSettings();

    Http::fake([
        'https://login.microsoftonline.com/*' => Http::response([
            'access_token' => 'refreshed-token',
            'refresh_token' => 'refreshed-refresh-token',
            'expires_in' => 3600,
        ]),
    ]);

    $calendar = createOutlookCalendar([
        'oauth_token_expires_at' => null,
    ]);

    $manager = new OutlookCalendarManager();
    $client = $manager->makeClient($calendar);

    expect($client)->toBeInstanceOf(Graph::class);

    $calendar->refresh();
    expect($calendar->oauth_token)->toBe('refreshed-token');
});

// ──────────────────────────────────────────────────
// revokeToken
// ──────────────────────────────────────────────────

it('clears all oauth fields when revoking token', function () {
    $calendar = createOutlookCalendar();

    $manager = new OutlookCalendarManager();
    $result = $manager->revokeToken($calendar);

    expect($result)->toBeTrue();

    $calendar->refresh();
    expect($calendar->oauth_token)->toBeNull()
        ->and($calendar->oauth_refresh_token)->toBeNull()
        ->and($calendar->oauth_token_expires_at)->toBeNull();
});
