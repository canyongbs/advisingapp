<?php

namespace AdvisingApp\Ai\Jobs;

use Illuminate\Bus\Queueable;
use AdvisingApp\Ai\Models\AiThread;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Spatie\Multitenancy\Jobs\TenantAware;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class ReInitializeAiThread implements ShouldQueue, TenantAware
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        protected AiThread $thread,
    ) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $this->thread->assistant->model->getService()->createThread($this->thread);
        $this->thread->save();
    }
}
