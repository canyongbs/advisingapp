<?php

/*
<COPYRIGHT>

    Copyright © 2016-2025, Canyon GBS LLC. All rights reserved.

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

use AdvisingApp\Ai\Exceptions\MessageResponseException;
use AdvisingApp\Ai\Models\AiAssistant;
use AdvisingApp\Ai\Models\AiMessage;
use AdvisingApp\Ai\Models\AiMessageFile;
use AdvisingApp\Ai\Models\AiThread;
use AdvisingApp\Ai\Services\Concerns\HasAiServiceHelpers;
use AdvisingApp\Ai\Services\Contracts\AiService;
use AdvisingApp\Ai\Settings\AiIntegrationsSettings;
use AdvisingApp\Ai\Settings\AiSettings;
use AdvisingApp\IntegrationOpenAi\DataTransferObjects\Assistants\AssistantsDataTransferObject;
use AdvisingApp\IntegrationOpenAi\DataTransferObjects\Threads\ThreadsDataTransferObject;
use AdvisingApp\IntegrationOpenAi\Exceptions\FileUploadsCannotBeDisabled;
use AdvisingApp\IntegrationOpenAi\Exceptions\FileUploadsCannotBeEnabled;
use AdvisingApp\Report\Enums\TrackedEventType;
use AdvisingApp\Report\Jobs\RecordTrackedEvent;
use Closure;
use Exception;
use Generator;
use Illuminate\Support\Facades\Http;
use Prism\Prism\Contracts\Message;
use Prism\Prism\Enums\ChunkType;
use Prism\Prism\Enums\FinishReason;
use Prism\Prism\Exceptions\PrismRateLimitedException;
use Prism\Prism\Prism;
use Prism\Prism\ValueObjects\Messages\AssistantMessage;
use Prism\Prism\ValueObjects\Messages\UserMessage;
use Throwable;

abstract class BaseOpenAiResponsesService implements AiService
{
    use HasAiServiceHelpers;

    public const FORMATTING_INSTRUCTIONS = 'When you answer, it is crucial that you format your response using rich text in markdown format. Do not ever mention in your response that the answer is being formatted/rendered in markdown.';

    public function __construct(
        protected AiIntegrationsSettings $settings,
    ) {}

    abstract public function getApiKey(): string;

    abstract public function getApiVersion(): string;

    abstract public function getModel(): string;

    public function complete(string $prompt, string $content): string
    {
        $aiSettings = app(AiSettings::class);

        try {
            $response = Prism::text()
                ->using('azure_open_ai', $this->getModel())
                ->withClientOptions([
                    'apiKey' => $this->getApiKey(),
                    'apiVersion' => $this->getApiVersion(),
                    'deployment' => $this->getDeployment(),
                ])
                ->withSystemPrompt($prompt)
                ->withPrompt($content)
                ->withMaxTokens($aiSettings->max_tokens->getTokens())
                ->usingTemperature($aiSettings->temperature)
                ->asText();
        } catch (PrismRateLimitedException $exception) {
            foreach ($exception->rateLimits as $rateLimit) {
                if ($rateLimit->resetsAt?->isFuture()) {
                    throw new MessageResponseException("Rate limit exceeded, retry at {$rateLimit->resetsAt}.");
                }
            }

            throw new MessageResponseException('Rate limit exceeded, please try again later.');
        } catch (Throwable $exception) {
            report($exception);

            throw new MessageResponseException('Failed to complete the prompt: [' . $exception->getMessage() . '].');
        }

        dispatch(new RecordTrackedEvent(
            type: TrackedEventType::AiExchange,
            occurredAt: now(),
        ));

        return $response->text;
    }

    /**
     * @param array<AiMessageFile> $files
     */
    public function sendMessage(AiMessage $message, array $files, Closure $saveResponse, ?UserMessage $userMessage = null): Closure
    {
        $previousMessages = [];

        $previousResponseId = $this->getMessagePreviousResponseId($message);

        if (blank($previousResponseId)) {
            $previousMessages = $message->thread->messages()
                ->oldest()
                ->get()
                ->map(fn (AiMessage $message): Message => filled($message->user_id)
                    ? new UserMessage($message->content)
                    : new AssistantMessage($message->content))
                ->all();
        }

        $aiSettings = app(AiSettings::class);
        $instructions = $this->generateAssistantInstructions($message->thread->assistant, withDynamicContext: true);

        try {
            $stream = Prism::text()
                ->using('azure_open_ai', $this->getModel())
                ->withClientOptions([
                    'apiKey' => $this->getApiKey(),
                    'apiVersion' => $this->getApiVersion(),
                    'deployment' => $this->getDeployment(),
                ])
                ->withSystemPrompt($instructions)
                ->withMessages([
                    ...$previousMessages,
                    $userMessage ?? new UserMessage($message->content),
                ])
                ->withMaxTokens($aiSettings->max_tokens->getTokens())
                ->usingTemperature($aiSettings->temperature)
                ->withProviderOptions(([
                    'previous_response_id' => $previousResponseId,
                ]))
                ->asStream();

            return $this->streamResponse($stream, $message, $saveResponse);
        } catch (Throwable $exception) {
            report($exception);

            throw new MessageResponseException('Failed to complete the prompt: [' . $exception->getMessage() . '].');
        } finally {
            try {
                if (is_null($message->thread->name)) {
                    $prompt = $message->context . "\nThe following is the start of a chat between you and a user:\n" . $message->content;

                    $message->thread->name = $this->complete($prompt, 'Generate a title for this chat, in 5 words or less. Do not respond with any greetings or salutations, and do not include any additional information or context. Just respond with the title:');

                    $message->thread->saved_at = now();

                    $message->thread->save();

                    dispatch(new RecordTrackedEvent(
                        type: TrackedEventType::AiThreadSaved,
                        occurredAt: now(),
                    ));
                }
            } catch (Exception $exception) {
                report($exception);

                $message->thread->name = 'Untitled Chat';
            }

            $message->context = $instructions;
            $message->save();

            dispatch(new RecordTrackedEvent(
                type: TrackedEventType::AiExchange,
                occurredAt: now(),
            ));
        }
    }

    public function getMessagePreviousResponseId(AiMessage $message): ?string
    {
        $previousResponseId = $message->thread->messages()
            ->whereDoesntHave('user')
            ->whereKeyNot($message)
            ->latest()
            ->first()
            ?->message_id;

        if (blank($previousResponseId)) {
            return null;
        }

        if (! str_starts_with($previousResponseId, 'resp_')) {
            return null;
        }

        try {
            $response = Http::withHeaders([
                'api-key' => $this->getApiKey(),
                'api-version' => $this->getApiVersion(),
            ])
                ->withQueryParameters(['api-version' => 'preview'])
                ->baseUrl($this->getDeployment())
                ->get("responses/{$previousResponseId}");

            return $response->ok() ? $previousResponseId : null;
        } catch (Throwable $exception) {
            report($exception);

            return null;
        }
    }

    /**
     * @param array<AiMessageFile> $files
     */
    public function completeResponse(AiMessage $response, array $files, Closure $saveResponse): Closure
    {
        return $this->sendMessage($response, $files, $saveResponse, new UserMessage('Continue generating the response, do not mention that I told you as I will paste it directly after the last message.'));
    }

    /**
     * @param array<AiMessageFile> $files
     */
    public function retryMessage(AiMessage $message, array $files, Closure $saveResponse): Closure
    {
        return $this->sendMessage($message, $files, $saveResponse);
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

    public function createAssistant(AiAssistant $assistant): void {}

    public function updateAssistant(AiAssistant $assistant): void {}

    public function retrieveAssistant(AiAssistant $assistant): ?AssistantsDataTransferObject
    {
        return null;
    }

    /**
     * @param array<string> $tools
     */
    public function updateAssistantTools(AiAssistant $assistant, array $tools): void {}

    public function enableAssistantFileUploads(AiAssistant $assistant): void
    {
        throw new FileUploadsCannotBeEnabled();
    }

    public function disableAssistantFileUploads(AiAssistant $assistant): void
    {
        throw new FileUploadsCannotBeDisabled();
    }

    public function createThread(AiThread $thread): void {}

    public function retrieveThread(AiThread $thread): ?ThreadsDataTransferObject
    {
        return null;
    }

    /**
     * @param array<string, mixed> $parameters
     */
    public function modifyThread(AiThread $thread, array $parameters): ?ThreadsDataTransferObject
    {
        return null;
    }

    public function deleteThread(AiThread $thread): void {}

    public function isAssistantExisting(AiAssistant $assistant): bool
    {
        return true;
    }

    public function isThreadExisting(AiThread $thread): bool
    {
        return true;
    }

    public function supportsMessageFileUploads(): bool
    {
        return false;
    }

    public function supportsAssistantFileUploads(): bool
    {
        return false;
    }

    protected function streamResponse(Generator $stream, AiMessage $message, Closure $saveResponse): Closure
    {
        return function () use ($message, $saveResponse, $stream): Generator {
            try {
                // If the message was sent by the user, save the response to a new record.
                // If the message was sent by the assistant, and we are completing the response, save it to the existing record.
                $response = filled($message->user_id) ? (new AiMessage()) : $message;

                foreach ($stream as $chunk) {
                    if (
                        ($chunk->chunkType === ChunkType::Meta) &&
                        filled($chunk->meta?->id)
                    ) {
                        $response->message_id = $chunk->meta->id;

                        continue;
                    }

                    if ($chunk->chunkType !== ChunkType::Text) {
                        continue;
                    }

                    yield json_encode(['type' => 'content', 'content' => base64_encode($chunk->text)]);
                    $response->content .= $chunk->text;

                    if ($chunk->finishReason === FinishReason::Length) {
                        yield json_encode(['type' => 'content', 'content' => base64_encode('...'), 'incomplete' => true]);
                        $response->content .= '...';
                    }

                    if ($chunk->finishReason === FinishReason::Error) {
                        yield json_encode(['type' => 'failed', 'message' => 'An error happened when sending your message.']);

                        report(new MessageResponseException('Stream not successful.'));
                    }
                }

                $saveResponse($response);
            } catch (PrismRateLimitedException $exception) {
                foreach ($exception->rateLimits as $rateLimit) {
                    if ($rateLimit->resetsAt?->isFuture()) {
                        yield json_encode(['type' => 'rate_limited', 'message' => 'Heavy traffic, just a few more moments...', 'retry_after_seconds' => now()->diffInSeconds($rateLimit->resetsAt) + 1]);

                        return;
                    }
                }

                yield json_encode(['type' => 'failed', 'message' => 'An error happened when sending your message.']);

                report(new MessageResponseException('Thread run was rate limited, but the system was unable to extract the number of retry seconds: [' . $exception->getMessage() . '].'));
            } catch (Throwable $exception) {
                yield json_encode(['type' => 'failed', 'message' => 'An error happened when sending your message.']);

                report($exception);
            }
        };
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
