<?php

namespace AdvisingApp\Ai\Listeners;

use AdvisingApp\Ai\Events\AiThreadForceDeleting;
use AdvisingApp\IntegrationOpenAi\Services\Concerns\UploadsFiles;

class DeleteAiThreadVectorStores
{
    public function handle(AiThreadForceDeleting $event): void
    {
        $service = $event->aiThread->assistant->model->getService();

        if ($service->supportsMessageFileUploads() && in_array(UploadsFiles::class, class_uses_recursive($service::class))) {
            /** @var ThreadsDataTransferObject $thread */
            $thread = $service->retrieveThread($event->aiThread);

            foreach ($thread->vectorStoreIds as $vectorStoreId) {
                $service->deleteVectorStore($vectorStoreId);
            }
        }
    }
}
