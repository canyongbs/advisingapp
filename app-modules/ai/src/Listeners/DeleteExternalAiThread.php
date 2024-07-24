<?php

namespace AdvisingApp\Ai\Listeners;

use AdvisingApp\Ai\Events\AiThreadForceDeleting;

class DeleteExternalAiThread
{
    public function handle(AiThreadForceDeleting $event): void
    {
        $service = $event->aiThread->assistant->model->getService();

        if ($service->isThreadExisting($event->aiThread)) {
            $service->deleteThread($event->aiThread);
        }
    }
}
