<?php

/*
<COPYRIGHT>

    Copyright © 2016-2026, Canyon GBS LLC. All rights reserved.

    Advising App™ is licensed under the Elastic License 2.0. For more details,
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
    - Canyon GBS LLC respects the intellectual property rights of others and expects the
      same in return. Canyon GBS™ and Advising App™ are registered trademarks of
      Canyon GBS LLC, and we are committed to enforcing and protecting our trademarks
      vigorously.
    - The software solution, including services, infrastructure, and code, is offered as a
      Software as a Service (SaaS) by Canyon GBS LLC.
    - Use of this software implies agreement to the license terms and conditions as stated
      in the Elastic License 2.0.

    For more information or inquiries please visit our website at
    https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

use AdvisingApp\MeetingCenter\Managers\OutlookCalendarManager;
use AdvisingApp\MeetingCenter\Models\Calendar;
use App\Models\User;
use Microsoft\Graph\Graph;
use Microsoft\Graph\Http\GraphCollectionRequest;
use Microsoft\Graph\Http\GraphResponse;
use Microsoft\Graph\Model\DateTimeTimeZone;
use Microsoft\Graph\Model\Event;
use Mockery\MockInterface;

function createMockOutlookEvent(string $id, string $subject): Event
{
    $start = new DateTimeTimeZone();
    $start->setDateTime('2026-03-05T10:00:00');
    $start->setTimeZone('UTC');

    $end = new DateTimeTimeZone();
    $end->setDateTime('2026-03-05T11:00:00');
    $end->setTimeZone('UTC');

    $event = new Event();
    $event->setId($id);
    $event->setSubject($subject);
    $event->setBodyPreview('Test body');
    $event->setStart($start);
    $event->setEnd($end);
    $event->setAttendees([]);

    return $event;
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

    $calendar = Calendar::factory()
        ->for(User::factory())
        ->create([
            'oauth_token' => 'test-token',
            'oauth_token_expires_at' => now()->addHour(),
        ]);

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
