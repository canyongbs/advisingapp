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

use OpenAI\Client;
use AdvisingApp\Ai\Models\AiThread;
use AdvisingApp\Ai\Models\AiMessage;
use AdvisingApp\Ai\Models\AiAssistant;
use AdvisingApp\Ai\Settings\AISettings;
use AdvisingApp\Ai\Services\Contracts\AiService;
use OpenAI\Responses\Threads\Runs\ThreadRunResponse;
use AdvisingApp\Ai\Exceptions\MessageResponseTimeoutException;

abstract class BaseOpenAiService implements AiService
{
    public const FORMATTING_INSTRUCTIONS = 'When you answer, it is crucial that you format your response using rich text in markdown format. Do not ever mention in your response that the answer is being formatted/rendered in markdown.';

    protected Client $client;

    abstract public function getModel(): string;

    public function createAssistant(AiAssistant $assistant): void
    {
        $response = $this->client->assistants()->create([
            'name' => $assistant->name,
            'instructions' => $this->generateAssistantInstructions($assistant),
            'model' => $this->getModel(),
            'metadata' => [
                'last_updated_at' => now(),
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

    public function createThread(AiThread $thread): void
    {
        $response = $this->client->threads()->create([]);

        $thread->thread_id = $response->id;
    }

    public function deleteThread(AiThread $thread): void
    {
        $this->client->threads()->delete($thread->thread_id);

        $thread->thread_id = null;
    }

    public function sendMessage(AiMessage $message): AiMessage
    {
        $response = $this->client->threads()->messages()->create($message->thread->thread_id, [
            'role' => 'user',
            'content' => $message->content,
        ]);

        $message->message_id = $response->id;
        $message->save();

        $response = $this->client->threads()->runs()->create($message->thread->thread_id, [
            'assistant_id' => $message->thread->assistant->assistant_id,
            'instructions' => $this->generateAssistantInstructions($message->thread->assistant, withDynamicContext: true),
        ]);

        $this->awaitThreadRunCompletion($response);

        $response = $this->client->threads()->messages()->list($message->thread->thread_id, [
            'order' => 'desc',
            'limit' => 1,
        ]);
        $responseContent = $response->data[0]->content[0]->text->value;

        $response = new AiMessage();
        $response->content = $responseContent;

        return $response;
    }

    public function retryMessage(AiMessage $message): AiMessage
    {
        $response = $this->client->threads()->runs()->list($message->thread->thread_id, [
            'order' => 'desc',
            'limit' => 1,
        ])->data[0] ?? null;

        if ((! $response) || $response?->status === 'completed') {
            if (blank($message->message_id)) {
                $response = $this->client->threads()->messages()->create($message->thread->thread_id, [
                    'role' => 'user',
                    'content' => $message->content,
                ]);

                $message->message_id = $response->id;
                $message->save();
            }

            $response = $this->client->threads()->runs()->create($message->thread->thread_id, [
                'assistant_id' => $message->thread->assistant->assistant_id,
                'instructions' => $this->generateAssistantInstructions($message->thread->assistant, withDynamicContext: true),
            ]);
        }

        $this->awaitThreadRunCompletion($response);

        $response = $this->client->threads()->messages()->list($message->thread->thread_id, [
            'order' => 'desc',
            'limit' => 1,
        ]);
        $responseContent = $response->data[0]->content[0]->text->value;

        $response = new AiMessage();
        $response->content = $responseContent;

        return $response;
    }

    public function getMaxAssistantInstructionsLength(): int
    {
        $limit = 32768;

        $limit -= strlen(resolve(AISettings::class)->prompt_system_context);
        $limit -= strlen(auth()->user()->getDynamicContext());
        $limit -= strlen(static::FORMATTING_INSTRUCTIONS);

        $limit -= 250; // For good measure.
        $limit -= ($limit % 100); // Round down to the nearest 100.

        return $limit;
    }

    protected function awaitThreadRunCompletion(ThreadRunResponse $threadRunResponse): void
    {
        $runId = $threadRunResponse->id;

        $timeout = 20;

        while ($threadRunResponse->status !== 'completed') {
            if ($timeout <= 0) {
                throw new MessageResponseTimeoutException();
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
