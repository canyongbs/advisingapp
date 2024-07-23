<?php

namespace AdvisingApp\Ai\Jobs;

use Illuminate\Bus\Queueable;
use AdvisingApp\Ai\Models\AiThread;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class DeleteExternalAiThread implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;

    public function __construct(public AiThread $aiThread)
    {
        $aiThread->loadMissing('assistant');
    }

    public function handle(): void
    {
        $service = $this->aiThread->assistant->model->getService();

        if ($service->isThreadExisting($this->aiThread)) {
            $service->deleteThread($this->aiThread);
        }
    }
}
