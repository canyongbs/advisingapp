<?php

namespace AdvisingApp\Ai\Jobs\QnaAdvisors;

use AdvisingApp\Ai\Models\QnaAdvisorLink;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Collection;

class UpdateCurrentQnaAdvisorLinks implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;

    public function handle(): void
    {
        QnaAdvisorLink::query()
            ->where('is_current', true)
            ->chunkById(100, function (Collection $links) {
                foreach ($links as $link) {
                    dispatch(new FetchQnaAdvisorLinkParsingResults($link));
                }
            });
    }
}