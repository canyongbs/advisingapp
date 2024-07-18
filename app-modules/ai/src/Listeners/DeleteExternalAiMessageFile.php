<?php

namespace AdvisingApp\Ai\Listeners;

use AdvisingApp\Ai\Models\AiThread;
use AdvisingApp\Ai\Models\AiMessage;
use AdvisingApp\Ai\Models\AiAssistant;
use Illuminate\Contracts\Queue\ShouldQueue;
use AdvisingApp\Ai\Events\AiMessageFileDeleted;
use AdvisingApp\IntegrationOpenAi\Services\Concerns\UploadsFiles;

class DeleteExternalAiMessageFile implements ShouldQueue
{
    public function handle(AiMessageFileDeleted $event): void
    {
        $aiMessageFile = $event->aiMessageFile;

        /** @var AiMessage $message */
        $message = $aiMessageFile->message()->withTrashed()->firstOrFail();

        /** @var AiThread $thread */
        $message->thread()->withTrashed()->firstOrFail();

        /** @var AiAssistant $assistant */
        $assistant = $thread->assistant()->withTrashed()->firstOrFail();

        $service = $assistant->model->getService();

        if ($service->supportsMessageFileUploads() && in_array(UploadsFiles::class, class_uses_recursive($service::class))) {
            $service->deleteFile($aiMessageFile);
        }
    }
}
