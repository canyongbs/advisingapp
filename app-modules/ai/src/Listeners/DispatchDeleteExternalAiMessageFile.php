<?php

namespace AdvisingApp\Ai\Listeners;

use Illuminate\Support\Facades\Bus;
use AdvisingApp\Ai\Events\AiMessageFileDeleted;
use AdvisingApp\Ai\Jobs\DeleteExternalAiMessageFile;

class DispatchDeleteExternalAiMessageFile
{
    public function handle(AiMessageFileDeleted $event): void
    {
        Bus::dispatch(new DeleteExternalAiMessageFile($event->aiMessageFile));
    }
}
