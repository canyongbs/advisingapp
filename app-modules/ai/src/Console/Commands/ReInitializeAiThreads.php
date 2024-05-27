<?php

namespace AdvisingApp\Ai\Console\Commands;

use Illuminate\Console\Command;
use AdvisingApp\Ai\Models\AiThread;
use AdvisingApp\Ai\Jobs\ReInitializeAiThread;
use Spatie\Multitenancy\Commands\Concerns\TenantAware;

class ReInitializeAiThreads extends Command
{
    use TenantAware;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ai:re-initialize-threads {--tenant=*}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Re-initialise all AI threads in the system.';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        AiThread::query()->eachById(function (AiThread $thread) {
            dispatch(new ReInitializeAiThread($thread));
        }, count: 250);

        return static::SUCCESS;
    }
}
