<?php

namespace AdvisingApp\Ai\Listeners;

class DeleteExternalAiMessageFile
{
    public function handle(object $event): void
    {
        if (empty($this->aiMessageFile->file_id)) {
            return;
        }

        $service = $this->aiMessageFile->message->thread->assistant->model->getService();

        if ($service->supportsMessageFileUploads() && in_array(UploadsFiles::class, class_uses_recursive($service::class))) {
            $service->deleteFile($this->aiMessageFile);
        }
    }
}
