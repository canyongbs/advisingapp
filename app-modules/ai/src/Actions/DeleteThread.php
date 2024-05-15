<?php

namespace AdvisingApp\Ai\Actions;

use AdvisingApp\Ai\Models\AiThread;

class DeleteThread
{
    public function __invoke(AiThread $thread): void
    {
        $thread->assistant->model->getService()->deleteThread($thread);
        $thread->delete();
    }
}
