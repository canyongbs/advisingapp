<?php

/*
<COPYRIGHT>

    Copyright © 2016-2024, Canyon GBS LLC. All rights reserved.

    Advising App™ is licensed under the Elastic License 2.0. For more details,
    see https://github.com/canyongbs/advisingapp/blob/main/LICENSE.

    Notice:

    - You may not provide the software to third parties as a hosted or managed
      service, where the service provides users with access to any substantial set of
      the features or functionality of the software.
    - You may not move, change, disable, or circumvent the license key functionality
      in the software, and you may not remove or obscure any functionality in the
      software that is protected by the license key.
    - You may not alter, remove, or obscure any licensing, copyright, or other notices
      of the licensor in the software. Any use of the licensor’s trademarks is subject
      to applicable law.
    - Canyon GBS LLC respects the intellectual property rights of others and expects the
      same in return. Canyon GBS™ and Advising App™ are registered trademarks of
      Canyon GBS LLC, and we are committed to enforcing and protecting our trademarks
      vigorously.
    - The software solution, including services, infrastructure, and code, is offered as a
      Software as a Service (SaaS) by Canyon GBS LLC.
    - Use of this software implies agreement to the license terms and conditions as stated
      in the Elastic License 2.0.

    For more information or inquiries please visit our website at
    https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

namespace AdvisingApp\IntegrationOpenAi\Jobs;

use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Collection;
use AdvisingApp\Ai\Models\AiAssistant;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use AdvisingApp\Ai\Services\Contracts\AiService;
use AdvisingApp\IntegrationOpenAi\Services\OpenAiGpt4oService;

/**
 * This cannot be queued due to an issue with serialization and CURL
 */
class UploadFilesToAssistant
{
    use Batchable;
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public $deleteWhenMissingModels = true;

    public $maxExceptions = 3;

    public function __construct(
        protected AiService $service,
        protected AiAssistant $assistant,
        protected Collection $files
    ) {}

    public function handle(): void
    {
        /** @var OpenAiGpt4oService $service */
        $service = $this->service;

        $assistantFiles = $service->createAssistantFiles(
            assistant: $this->assistant,
            files: $this->files
        );

        $response = $service->retrieveAssistant($this->assistant);

        $vectorStoreId = $response->toolResources->fileSearch['vectorStoreIds'][0] ?? null;

        $vectorStore = null;

        //  Create the vector store if the assistant doesn't already have one
        if (blank($vectorStoreId)) {
            $vectorStore = $service->createVectorStore([
                'file_ids' => $assistantFiles->pluck('file_id')->toArray(),
                'name' => $this->assistant->name . 'Vector Store',
            ]);

            $service->updateAssistantVectorStoreId(
                assistant: $this->assistant,
                vectorStoreId: $vectorStore->id
            );
        } else {
            $service->createVectorStoreFilesBatch(
                service: $this->service,
                vectorStoreId: $vectorStoreId,
                fileIds: $assistantFiles->pluck('file_id')->toArray()
            );
        }

        if (blank($vectorStore)) {
            $vectorStore = $service->retrieveVectorStore($vectorStoreId);
        }

        $service->awaitVectorStoreProcessing(
            service: $this->service,
            vectorStore: $vectorStore,
        );

        // TODO Emit event for notification that files finished uploading
    }
}
