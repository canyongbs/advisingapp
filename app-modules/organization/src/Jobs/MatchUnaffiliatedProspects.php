<?php

namespace AdvisingApp\Organization\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class MatchUnaffiliatedProspects implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public ?string $organizationId = null,
    ) {}

    public function handle(): void {}
}
