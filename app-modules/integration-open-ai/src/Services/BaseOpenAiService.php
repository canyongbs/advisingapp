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

use AdvisingApp\Ai\Models\AiThread;
use AdvisingApp\Ai\Models\AiMessage;
use Illuminate\Support\Facades\Http;
use OpenAI\Contracts\ClientContract;
use AdvisingApp\Ai\Models\AiAssistant;
use AdvisingApp\Ai\Settings\AiSettings;
use AdvisingApp\Ai\Models\AiMessageFile;
use OpenAI\Responses\Threads\ThreadResponse;
use AdvisingApp\Ai\Services\Contracts\AiService;
use OpenAI\Responses\Threads\Runs\ThreadRunResponse;
use AdvisingApp\Ai\Exceptions\MessageResponseException;
use AdvisingApp\Ai\Services\Concerns\HasAiServiceHelpers;
use AdvisingApp\Ai\Exceptions\MessageResponseTimeoutException;
use AdvisingApp\Ai\DataTransferObjects\Files\FilesDataTransferObject;
use AdvisingApp\Ai\DataTransferObjects\Threads\ThreadsDataTransferObject;
use AdvisingApp\Ai\DataTransferObjects\VectorStores\VectorStoresDataTransferObject;
use AdvisingApp\Ai\DataTransferObjects\VectorStoreFiles\VectorStoreFilesDataTransferObject;

abstract class BaseOpenAiService implements AiService
{
    use HasAiServiceHelpers;

    public const FORMATTING_INSTRUCTIONS = 'When you answer, it is crucial that you format your response using rich text in markdown format. Do not ever mention in your response that the answer is being formatted/rendered in markdown.';

    protected ClientContract $client;

    abstract public function getApiKey(): string;

    abstract public function getApiVersion(): string;

    abstract public function getDeployment(): string;

    abstract public function getModel(): string;

    public function getClient(): ClientContract
    {
        return $this->client;
    }

    public function createAssistant(AiAssistant $assistant): void
    {
        $response = $this->client->assistants()->create([
            'name' => $assistant->name,
            'instructions' => $this->generateAssistantInstructions($assistant),
            'model' => $this->getModel(),
            'metadata' => [
                'last_updated_at' => now(),
            ],
            'tools' => [
                [
                    'type' => 'file_search',
                ],
            ],
        ]);

        $assistant->assistant_id = $response->id;
    }

    public function updateAssistant(AiAssistant $assistant): void
    {
        $this->client->assistants()->modify($assistant->assistant_id, [
            'instructions' => $this->generateAssistantInstructions($assistant),
            'name' => $assistant->name,
            'model' => $this->getModel(),
        ]);
    }

    public function updateAssistantTools(AiAssistant $assistant, array $tools): void
    {
        $tools = collect($tools)->map(function ($tool) {
            return [
                'type' => $tool,
            ];
        })->toArray();

        $this->client->assistants()->modify($assistant->assistant_id, [
            'tools' => $tools,
        ]);
    }

    public function createThread(AiThread $thread): void
    {
        $existingMessagePopulationLimit = 32;
        $existingMessages = [];
        $existingMessagesOverflow = [];

        if ($thread->exists) {
            $allExistingMessages = $thread->messages()
                ->orderBy('id')
                ->get()
                ->toBase()
                ->map(fn (AiMessage $message): array => [
                    'content' => $message->content,
                    'role' => $message->user_id ? 'user' : 'assistant',
                ]);

            $existingMessages = $allExistingMessages
                ->take($existingMessagePopulationLimit)
                ->values()
                ->all();

            $existingMessagesOverflow = $allExistingMessages
                ->slice($existingMessagePopulationLimit)
                ->values()
                ->all();
        }

        $response = $this->client->threads()->create([
            'messages' => $existingMessages,
        ]);

        $thread->thread_id = $response->id;

        if (count($existingMessagesOverflow)) {
            foreach ($existingMessagesOverflow as $overflowMessage) {
                $this->client->threads()->messages()->create($thread->thread_id, $overflowMessage);
            }
        }
    }

    public function retrieveThread(AiThread $thread): ThreadsDataTransferObject
    {
        $response = $this->client->threads()->retrieve($thread->thread_id);

        return ThreadsDataTransferObject::from([
            'id' => $thread->thread_id,
            'vectorStoreIds' => $response->toolResources?->fileSearch?->vectorStoreIds ?? [],
        ]);
    }

    public function modifyThread(AiThread $thread, array $parameters): ThreadsDataTransferObject
    {
        /** @var ThreadResponse $response */
        $response = $this->client->threads()->modify($thread->thread_id, $parameters);

        return ThreadsDataTransferObject::from([
            'id' => $response->id,
            'vectorStoreIds' => $response->toolResources?->fileSearch?->vectorStoreIds ?? [],
        ]);
    }

    public function deleteThread(AiThread $thread): void
    {
        $this->client->threads()->delete($thread->thread_id);

        $thread->thread_id = null;
    }

    public function sendMessage(AiMessage $message, array $files = []): AiMessage
    {
        $response = $this->client->threads()->runs()->list($message->thread->thread_id, [
            'order' => 'desc',
            'limit' => 1,
        ])->data[0] ?? null;

        // An existing run might be in progress, so we need to wait for it to complete first.
        if ($response && (! in_array($response?->status, ['completed', 'failed', 'expired']))) {
            $this->awaitThreadRunCompletion($response);
        }

        $createdFiles = [];

        if (method_exists($this, 'createFiles') && ! empty($files)) {
            $createdFiles = $this->createFiles($message, $files);
        }

        $data = [
            'role' => 'user',
            'content' => $message->content,
        ];

        if (! empty($createdFiles)) {
            $data['attachments'] = collect($createdFiles)->map(function ($createdFile) {
                return [
                    'file_id' => $createdFile->file_id,
                    'tools' => [
                        [
                            'type' => 'file_search',
                        ],
                    ],
                ];
            })->toArray();
        }

        $response = $this->client->threads()->messages()->create($message->thread->thread_id, $data);

        $instructions = $this->generateAssistantInstructions($message->thread->assistant, withDynamicContext: true);

        $message->context = $instructions;
        $message->message_id = $response->id;
        $message->save();

        if (! empty($createdFiles)) {
            foreach ($createdFiles as $file) {
                $file->message()->associate($message);
                $file->save();
            }
        }

        $response = $this->client->threads()->runs()->create($message->thread->thread_id, [
            'assistant_id' => $message->thread->assistant->assistant_id,
            'instructions' => $instructions,
        ]);

        $this->awaitThreadRunCompletion($response);

        $response = $this->client->threads()->messages()->list($message->thread->thread_id, [
            'order' => 'desc',
            'limit' => 1,
        ]);
        $responseContent = $response->data[0]->content[0]->text->value;
        $responseId = $response->data[0]->id;

        $response = new AiMessage();
        $response->content = $responseContent;
        $response->message_id = $responseId;

        return $response;
    }

    public function retryMessage(AiMessage $message): AiMessage
    {
        $response = $this->client->threads()->runs()->list($message->thread->thread_id, [
            'order' => 'desc',
            'limit' => 1,
        ])->data[0] ?? null;

        if (in_array($response?->status, ['failed', 'expired'])) {
            report(new MessageResponseException('Thread run was not successful: [' . json_encode($response->toArray()) . '].'));
        }

        if (
            (! $response) ||
            in_array($response?->status, ['completed', 'failed', 'expired']) ||
            blank($message->message_id)
        ) {
            $instructions = $this->generateAssistantInstructions($message->thread->assistant, withDynamicContext: true);

            if (blank($message->message_id)) {
                $response = $this->client->threads()->messages()->create($message->thread->thread_id, [
                    'role' => 'user',
                    'content' => $message->content,
                ]);

                $message->context = $instructions;
                $message->message_id = $response->id;
                $message->save();
            }

            $response = $this->client->threads()->runs()->create($message->thread->thread_id, [
                'assistant_id' => $message->thread->assistant->assistant_id,
                'instructions' => $instructions,
            ]);
        }

        $this->awaitThreadRunCompletion($response);

        $response = $this->client->threads()->messages()->list($message->thread->thread_id, [
            'order' => 'desc',
            'limit' => 1,
        ]);
        $responseContent = $response->data[0]->content[0]->text->value;
        $responseId = $response->data[0]->id;

        $response = new AiMessage();
        $response->content = $responseContent;
        $response->message_id = $responseId;

        return $response;
    }

    public function getMaxAssistantInstructionsLength(): int
    {
        $limit = 32768;

        $limit -= strlen(resolve(AiSettings::class)->prompt_system_context);
        $limit -= strlen(static::FORMATTING_INSTRUCTIONS);

        $limit -= 600; // For good measure.
        $limit -= ($limit % 100); // Round down to the nearest 100.

        return $limit;
    }

    public function isAssistantExisting(AiAssistant $assistant): bool
    {
        return filled($assistant->assistant_id);
    }

    public function isThreadExisting(AiThread $thread): bool
    {
        return filled($thread->thread_id);
    }

    public function supportsFileUploads(): bool
    {
        return false;
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
        $response = $this->client->vectorStores()->modify($vectorStoreId, $parameters);
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

    protected function awaitThreadRunCompletion(ThreadRunResponse $threadRunResponse): void
    {
        $runId = $threadRunResponse->id;

        $timeout = 60;

        while ($threadRunResponse->status !== 'completed') {
            if ($timeout <= 0) {
                throw new MessageResponseTimeoutException();
            }

            if (in_array($threadRunResponse->status, ['failed', 'expired'])) {
                throw new MessageResponseException('Thread run not successful: [' . json_encode($threadRunResponse->toArray()) . '].');
            }

            usleep(500000);

            $threadRunResponse = $this->client->threads()->runs()->retrieve($threadRunResponse->threadId, $runId);

            $timeout -= 0.5;
        }
    }

    protected function generateAssistantInstructions(AiAssistant $assistant, bool $withDynamicContext = false): string
    {
        $assistantInstructions = rtrim($assistant->instructions, '. ');

        $maxAssistantInstructionsLength = $this->getMaxAssistantInstructionsLength();

        if (strlen($assistantInstructions) > $maxAssistantInstructionsLength) {
            $truncationEnd = '... [truncated]';

            $assistantInstructions = (string) str($assistantInstructions)
                ->limit($maxAssistantInstructionsLength - strlen($truncationEnd), $truncationEnd);
        }

        $formattingInstructions = static::FORMATTING_INSTRUCTIONS;

        if (! $withDynamicContext) {
            return "{$assistantInstructions}.\n\n{$formattingInstructions}";
        }

        $dynamicContext = rtrim(auth()->user()->getDynamicContext(), '. ');

        return "{$dynamicContext}.\n\n{$assistantInstructions}.\n\n{$formattingInstructions}";
    }
}
