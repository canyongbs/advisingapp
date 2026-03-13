<?php

use AdvisingApp\Ai\Jobs\QnaAdvisors\FetchQnaAdvisorLinkParsingResults;
use AdvisingApp\Ai\Jobs\QnaAdvisors\UpdateCurrentQnaAdvisorLinks;
use AdvisingApp\Ai\Models\QnaAdvisorLink;
use App\Models\Tenant;
use Illuminate\Console\Scheduling\CallbackEvent;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Queue;

it('will only run for QnA advisors that have is_current set to true', function () {
    $currentLink = QnaAdvisorLink::factory()->create([
        'is_current' => true,
    ]);

    $nonCurrentLink = QnaAdvisorLink::factory()->create([
        'is_current' => false,
    ]);

    expect($currentLink->is_current)->toBeTrue();
    expect($nonCurrentLink->is_current)->toBeFalse();

    (new UpdateCurrentQnaAdvisorLinks())->handle();

    $currentLink->refresh();
    $nonCurrentLink->refresh();

    expect($currentLink->is_current)->toBeTrue();
    expect($nonCurrentLink->is_current)->toBeFalse();
});

it('dispatches FetchQnaAdvisorLinkParsingResults only for current links', function () {
    Queue::fake();

    $currentLink = QnaAdvisorLink::factory()->create([
        'is_current' => true,
    ]);

    $nonCurrentLink = QnaAdvisorLink::factory()->create([
        'is_current' => false,
    ]);

    (new UpdateCurrentQnaAdvisorLinks())->handle();

    Queue::assertPushed(FetchQnaAdvisorLinkParsingResults::class, function (FetchQnaAdvisorLinkParsingResults $job) use ($currentLink) {
        return $job->uniqueId() === $currentLink->id
            && $job->refreshesExistingParsingResults();
    });

    Queue::assertNotPushed(FetchQnaAdvisorLinkParsingResults::class, function (FetchQnaAdvisorLinkParsingResults $job) use ($nonCurrentLink) {
        return $job->uniqueId() === $nonCurrentLink->id;
    });
});

it('is scheduled to dispatch UpdateCurrentQnaAdvisorLinks monthly on the first day at midnight', function () {
    $tenant = Tenant::firstOrFail();
    $schedule = app()->make(Schedule::class);

    $events = (new Collection($schedule->events()))->filter(function (mixed $event) use ($tenant) {
        return $event instanceof CallbackEvent
            && $event->getSummaryForDisplay() === "Dispatch UpdateCurrentQnaAdvisorLinks | Tenant {$tenant->domain}"
            && $event->expression === '0 0 1 * *';
    });

    expect($events)->toHaveCount(1);
});

it('dispatches UpdateCurrentQnaAdvisorLinks when its scheduled callback runs', function () {
    Bus::fake();

    $tenant = Tenant::firstOrFail();
    $schedule = app()->make(Schedule::class);

    $event = (new Collection($schedule->events()))
        ->first(function (mixed $event) use ($tenant) {
            return $event instanceof CallbackEvent
                && $event->getSummaryForDisplay() === "Dispatch UpdateCurrentQnaAdvisorLinks | Tenant {$tenant->domain}"
                && $event->expression === '0 0 1 * *';
        });

    expect($event)->toBeInstanceOf(CallbackEvent::class);

    $event->run(app());

    Bus::assertDispatched(UpdateCurrentQnaAdvisorLinks::class);
});
