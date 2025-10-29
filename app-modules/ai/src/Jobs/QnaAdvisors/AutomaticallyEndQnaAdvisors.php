<?php

namespace AdvisingApp\Ai\Jobs\QnaAdvisors;

use AdvisingApp\Ai\Events\QnaAdvisors\EndQnaAdvisorThread;
use AdvisingApp\Ai\Models\QnaAdvisorThread;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class AutomaticallyEndQnaAdvisors implements ShouldQueue
{
    public function handle(): void
    {
        //dispatch websocket event
        $threads = QnaAdvisorThread::whereNotNull('finished_at')
            ->whereHas(
                'latestMessage',
                fn (Builder $query) => $query->where('created_at', '<=', now()->subHour())
            )
            ->get();

        DB::transaction(function() use ($threads) {
            $threads->each(function (QnaAdvisorThread $thread) {
                $thread->finished_at = now();
                $thread->save();

                event(new EndQnaAdvisorThread($thread));
            });
        });
    }
}