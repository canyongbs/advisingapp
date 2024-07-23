<?php

namespace AdvisingApp\Ai\Jobs;

use Illuminate\Bus\Queueable;
use AdvisingApp\Ai\Models\AiThread;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use AdvisingApp\IntegrationOpenAi\Services\Concerns\UploadsFiles;
use AdvisingApp\IntegrationOpenAi\DataTransferObjects\Threads\ThreadsDataTransferObject;

class DeleteAiThreadVectorStores implements ShouldQueue
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

        if ($service->supportsMessageFileUploads() && in_array(UploadsFiles::class, class_uses_recursive($service::class))) {
            /** @var ThreadsDataTransferObject $thread */
            $thread = $service->retrieveThread($this->aiThread);

            foreach ($thread->vectorStoreIds as $vectorStoreId) {
                $service->deleteVectorStore($vectorStoreId);
            }
        }
    }
}
