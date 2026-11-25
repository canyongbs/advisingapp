<?php

use AdvisingApp\Organization\Jobs\DispatchMatchUnaffiliatedProspectsForEachTenant;
use AdvisingApp\Organization\Jobs\MatchUnaffiliatedProspects;
use Illuminate\Support\Facades\Queue;

it('dispatches `MatchUnaffiliatedProspects` for each tenant', function (): void {
    Queue::fake();

    (new DispatchMatchUnaffiliatedProspectsForEachTenant())->handle();

    Queue::assertPushed(
        MatchUnaffiliatedProspects::class,
        fn (MatchUnaffiliatedProspects $job): bool => $job->organizationId === null,
    );
    Queue::assertPushed(MatchUnaffiliatedProspects::class, 1);
});
