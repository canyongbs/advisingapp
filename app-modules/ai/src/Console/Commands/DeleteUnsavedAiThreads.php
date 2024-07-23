<?php

namespace AdvisingApp\Ai\Console\Commands;

use Illuminate\Console\Command;
use AdvisingApp\Ai\Models\AiThread;
use Spatie\Multitenancy\Commands\Concerns\TenantAware;

class DeleteUnsavedAiThreads extends Command
{
    use TenantAware;

    protected $signature = 'ai:delete-unsaved-ai-threads {--tenant=*}';

    protected $description = 'Finds unsaved AiThreads older than 3 days and marks them for deletion.';

    public function handle()
    {
        AiThread::query()
            ->whereNull('saved_at')
            ->where('created_at', '<=', now()->subDays(3))
            ->each(fn (AiThread $aiThread) => $aiThread->delete());
    }
}
