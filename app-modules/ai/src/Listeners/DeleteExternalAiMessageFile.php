<?php

namespace AdvisingApp\Ai\Listeners;

use AdvisingApp\Ai\Events\AiMessageFileForceDeleting;
use AdvisingApp\IntegrationOpenAi\Services\Concerns\UploadsFiles;

class DeleteExternalAiMessageFile
{
    public function handle(AiMessageFileForceDeleting $event): void
    {
        if (empty($event->aiMessageFile->file_id)) {
            return;
        }

        $service = $event->aiMessageFile->message->thread->assistant->model->getService();

        if ($service->supportsMessageFileUploads() && in_array(UploadsFiles::class, class_uses_recursive($service::class))) {
            $service->deleteFile($event->aiMessageFile);
        }
    }
}
