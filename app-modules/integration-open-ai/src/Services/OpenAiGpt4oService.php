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

namespace AdvisingApp\IntegrationOpenAi\Services;

use OpenAI;
use AdvisingApp\Ai\Models\AiThread;
use Illuminate\Support\Facades\Http;
use AdvisingApp\Ai\Models\AiMessageFile;
use AdvisingApp\Ai\Settings\AiIntegrationsSettings;
use AdvisingApp\Ai\Services\Contracts\SupportsFileUploads;
use AdvisingApp\IntegrationOpenAi\Services\Concerns\UploadsFiles;
use AdvisingApp\Ai\DataTransferObjects\Files\FilesDataTransferObject;
use AdvisingApp\Ai\DataTransferObjects\VectorStores\VectorStoresDataTransferObject;
use AdvisingApp\Ai\DataTransferObjects\VectorStoreFiles\VectorStoreFilesDataTransferObject;

class OpenAiGpt4oService extends BaseOpenAiService implements SupportsFileUploads
{
    use UploadsFiles;

    public function __construct(
        protected AiIntegrationsSettings $settings,
    ) {
        $this->client = OpenAI::factory()
            ->withBaseUri($this->getDeployment())
            ->withHttpHeader('api-key', $this->settings->open_ai_gpt_4o_api_key ?? config('integration-open-ai.gpt_4o_api_key'))
            ->withQueryParam('api-version', config('integration-open-ai.gpt_4o_api_version'))
            ->withHttpHeader('OpenAI-Beta', 'assistants=v2')
            ->withHttpHeader('Accept', '*/*')
            ->make();
    }

    public function retrieveFile(AiMessageFile $file): FilesDataTransferObject
    {
        $response = $this->client->files()->retrieve($file->file_id);

        return FilesDataTransferObject::from([
            'id' => $response->id,
            'name' => $response->filename,
            'status' => $response->status,
        ]);
    }

    public function createVectorStore(array $parameters): VectorStoresDataTransferObject
    {
        $response = $this->client->vectorStores()->create($parameters);

        return VectorStoresDataTransferObject::from([
            'id' => $response->id,
            'name' => $response->name,
            'fileCounts' => get_object_vars($response->fileCounts),
            'status' => $response->status,
            'expiresAt' => $response->expiresAt,
        ]);
    }

    public function retrieveVectorStore(string $vectorStoreId): VectorStoresDataTransferObject
    {
        $response = $this->client->vectorStores()->retrieve($vectorStoreId);

        return VectorStoresDataTransferObject::from([
            'id' => $response->id,
            'name' => $response->name,
            'fileCounts' => get_object_vars($response->fileCounts),
            'status' => $response->status,
            'expiresAt' => $response->expiresAt,
        ]);
    }

    public function modifyVectorStore(string $vectorStoreId, array $parameters): void
    {
        $this->client->vectorStores()->modify($vectorStoreId, $parameters);
    }

    /**
     * The `openai-php/client` does not current work with the `GET /vector_stores/{vectorStoreId}/files` endpoint
     * for Azure Open AI. This is due to the expectation of a `chunking_strategy` key in the response, which Azure
     * does not provide. An issue has been opened, but this request needs to happen without the client for now.
     */
    public function retrieveVectorStoreFiles(AiThread $thread, string $vectorStoreId, array $params): VectorStoreFilesDataTransferObject
    {
        $service = $thread->assistant->model->getService();

        $response = Http::withHeaders([
            'api-key' => $service->getApiKey(),
            'OpenAI-Beta' => 'assistants=v2',
            'Content-Type' => 'application/json',
        ])
            ->withQueryParameters(
                $params,
            )
            ->get($service->getDeployment() . '/vector_stores/' . $vectorStoreId . '/files', [
                'api-version' => $service->getApiVersion(),
            ]);

        return VectorStoreFilesDataTransferObject::from([
            'object' => $response->json()['object'],
            'data' => $response->json()['data'],
            'firstId' => $response->json()['first_id'],
            'lastId' => $response->json()['last_id'],
            'hasMore' => $response->json()['has_more'],
        ]);
    }

    public function getApiKey(): string
    {
        return $this->settings->open_ai_gpt_4o_api_key ?? config('integration-open-ai.gpt_4o_api_key');
    }

    public function getApiVersion(): string
    {
        return config('integration-open-ai.gpt_4o_api_version');
    }

    public function getModel(): string
    {
        return $this->settings->open_ai_gpt_4o_model ?? config('integration-open-ai.gpt_4o_model');
    }

    public function getDeployment(): string
    {
        return $this->settings->open_ai_gpt_4o_base_uri ?? config('integration-open-ai.gpt_4o_base_uri');
    }
}
