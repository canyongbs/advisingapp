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

use Closure;
use Generator;
use AdvisingApp\Ai\Models\AiThread;
use AdvisingApp\Ai\Models\AiMessage;
use OpenAI\Contracts\ClientContract;
use AdvisingApp\Ai\Models\AiAssistant;
use AdvisingApp\Ai\Settings\AiSettings;
use AdvisingApp\Ai\Services\Contracts\AiService;
use OpenAI\Responses\Threads\Runs\ThreadRunResponse;
use AdvisingApp\Ai\Exceptions\MessageResponseException;
use AdvisingApp\Ai\Services\Concerns\HasAiServiceHelpers;
use AdvisingApp\Ai\Exceptions\MessageResponseTimeoutException;

abstract class BaseOpenAiService implements AiService
{
    use HasAiServiceHelpers;

    public const FORMATTING_INSTRUCTIONS = 'When you answer, it is crucial that you format your response using rich text in markdown format. Do not ever mention in your response that the answer is being formatted/rendered in markdown.';

    protected ClientContract $client;

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

    public function deleteThread(AiThread $thread): void
    {
        $this->client->threads()->delete($thread->thread_id);

        $thread->thread_id = null;
    }

    public function sendMessage(AiMessage $message, Closure $saveResponse): Closure
    {
        $response = $this->client->threads()->runs()->list($message->thread->thread_id, [
            'order' => 'desc',
            'limit' => 1,
        ])->data[0] ?? null;

        // An existing run might be in progress, so we need to wait for it to complete first.
        if ($response && (! in_array($response?->status, ['completed', 'failed', 'expired', 'cancelled', 'incomplete']))) {
            $this->awaitThreadRunCompletion($response);
        }

        $response = $this->client->threads()->messages()->create($message->thread->thread_id, [
            'role' => 'user',
            'content' => $message->content,
        ]);

        $instructions = $this->generateAssistantInstructions($message->thread->assistant, withDynamicContext: true);

        $message->context = $instructions;
        $message->message_id = $response->id;
        $message->save();

        $aiSettings = app(AiSettings::class);

        $stream = $this->client->threads()->runs()->createStreamed($message->thread->thread_id, [
            'assistant_id' => $message->thread->assistant->assistant_id,
            'instructions' => $instructions,
            'max_completion_tokens' => $aiSettings->max_tokens,
            'temperature' => $aiSettings->temperature,
        ]);

        return function () use ($saveResponse, $stream): Generator {
            $response = new AiMessage();

            foreach ($stream as $streamResponse) {
                if ($streamResponse->event === 'thread.message.delta') {
                    foreach ($streamResponse->response->delta->content as $content) {
                        yield $content->text->value;
                        $response->content .= $content->text->value;
                    }

                    $response->message_id = $streamResponse->response->id;
                } elseif ($streamResponse->event === 'thread.message.incomplete') {
                    yield '...';
                    $response->content .= '...';
                } elseif ($streamResponse->event === 'thread.message.completed') {
                    $response->content = $streamResponse->response->content[0]?->text->value;
                    $response->message_id = $streamResponse->response->id;
                } elseif (in_array($streamResponse->event, [
                    'thread.run.expired',
                    'thread.run.step.expired',
                ])) {
                    throw new MessageResponseTimeoutException();
                } elseif (in_array($streamResponse->event, [
                    'thread.run.failed',
                    'thread.run.cancelling',
                    'thread.run.cancelled',
                    'thread.run.step.failed',
                    'thread.run.step.cancelled',
                ])) {
                    throw new MessageResponseException('Thread run not successful: [' . json_encode($streamResponse->response->toArray()) . '].');
                }
            }

            $saveResponse($response);
        };
    }

    public function retryMessage(AiMessage $message, Closure $saveResponse): Closure
    {
        $response = $this->client->threads()->runs()->list($message->thread->thread_id, [
            'order' => 'desc',
            'limit' => 1,
        ])->data[0] ?? null;

        if (in_array($response?->status, ['failed', 'expired', 'cancelled', 'incomplete'])) {
            report(new MessageResponseException('Thread run was not successful: [' . json_encode($response->toArray()) . '].'));
        }

        if (
            $response &&
            (! in_array($response?->status, ['completed', 'failed', 'expired', 'cancelled', 'incomplete'])) &&
            filled($message->message_id)
        ) {
            $this->awaitThreadRunCompletion($response);

            $response = $this->client->threads()->messages()->list($message->thread->thread_id, [
                'order' => 'desc',
                'limit' => 1,
            ]);
            $responseContent = $response->data[0]->content[0]->text->value;
            $responseId = $response->data[0]->id;

            return function () use ($responseContent, $responseId, $saveResponse): Generator {
                $response = new AiMessage();

                yield $responseContent;

                $response->content = $responseContent;
                $response->message_id = $responseId;

                $saveResponse($response);
            };
        }

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

        $aiSettings = app(AiSettings::class);

        $stream = $this->client->threads()->runs()->createStreamed($message->thread->thread_id, [
            'assistant_id' => $message->thread->assistant->assistant_id,
            'instructions' => $instructions,
            'max_completion_tokens' => $aiSettings->max_tokens,
            'temperature' => $aiSettings->temperature,
        ]);

        return function () use ($saveResponse, $stream): Generator {
            $response = new AiMessage();

            foreach ($stream as $streamResponse) {
                if ($streamResponse->event === 'thread.message.delta') {
                    foreach ($streamResponse->response->delta->content as $content) {
                        yield $content->text->value;
                        $response->content .= $content->text->value;
                    }

                    $response->message_id = $streamResponse->response->id;
                } elseif ($streamResponse->event === 'thread.message.incomplete') {
                    yield '...';
                    $response->content .= '...';
                } elseif ($streamResponse->event === 'thread.message.completed') {
                    $response->content = $streamResponse->response->content[0]?->text->value;
                    $response->message_id = $streamResponse->response->id;
                } elseif (in_array($streamResponse->event, [
                    'thread.run.expired',
                    'thread.run.step.expired',
                ])) {
                    throw new MessageResponseTimeoutException();
                } elseif (in_array($streamResponse->event, [
                    'thread.run.failed',
                    'thread.run.cancelling',
                    'thread.run.cancelled',
                    'thread.run.step.failed',
                    'thread.run.step.cancelled',
                ])) {
                    throw new MessageResponseException('Thread run not successful: [' . json_encode($streamResponse->response->toArray()) . '].');
                }
            }

            $saveResponse($response);
        };
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

    protected function awaitThreadRunCompletion(ThreadRunResponse $threadRunResponse): void
    {
        $runId = $threadRunResponse->id;

        // 60 second total request timeout, with a 10-second buffer.
        $currentTime = time();
        $requestTime = app()->runningUnitTests() ? time() : $_SERVER['REQUEST_TIME'];
        $timeoutInSeconds = 60 - ($currentTime - $requestTime) - 10;
        $expiration = $currentTime + $timeoutInSeconds;

        while ($threadRunResponse->status !== 'completed') {
            if (time() >= $expiration) {
                throw new MessageResponseTimeoutException();
            }

            if (in_array($threadRunResponse->status, ['failed', 'expired', 'cancelled', 'incomplete'])) {
                throw new MessageResponseException('Thread run not successful: [' . json_encode($threadRunResponse->toArray()) . '].');
            }

            usleep(500000);

            $threadRunResponse = $this->client->threads()->runs()->retrieve($threadRunResponse->threadId, $runId);
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
