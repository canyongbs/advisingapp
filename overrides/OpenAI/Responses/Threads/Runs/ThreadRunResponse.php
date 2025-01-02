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

declare(strict_types = 1);

namespace OpenAI\Responses\Threads\Runs;

use OpenAI\Contracts\ResponseContract;
use OpenAI\Contracts\ResponseHasMetaInformationContract;
use OpenAI\Responses\Assistants\AssistantResponseResponseFormat;
use OpenAI\Responses\Concerns\ArrayAccessible;
use OpenAI\Responses\Concerns\HasMetaInformation;
use OpenAI\Responses\Meta\MetaInformation;
use OpenAI\Testing\Responses\Concerns\Fakeable;

/**
 * @implements ResponseContract<array{id: string, object: string, created_at: int, thread_id: string, assistant_id: string, status: string, required_action?: array{type: string, submit_tool_outputs: array{tool_calls: array<int, array{id: string, type: string, function: array{name: string, arguments: string}}>}}, last_error: ?array{code: string, message: string}, expires_at: ?int, started_at: ?int, cancelled_at: ?int, failed_at: ?int, completed_at: ?int, model: string, instructions: ?string, tools: array<int, array{type: string}|array{type: string}|array{type: string, function: array{description: string, name: string, parameters: array<string, mixed>}}>, metadata: array<string, string>, usage?: array{prompt_tokens: int, completion_tokens: int|null, total_tokens: int}, incomplete_details: null|array{reason: string}, temperature: float|int|null, top_p: null|float|int, max_prompt_tokens: ?int, max_completion_tokens: ?int, truncation_strategy: ?array{type: string, last_messages: ?int}, tool_choice: null|string|array{type: string, function?: array{name: string}}, response_format: null|string|array{type: string}}>
 */
final class ThreadRunResponse implements ResponseContract, ResponseHasMetaInformationContract
{
    /**
     * @use ArrayAccessible<array{id: string, object: string, created_at: int, thread_id: string, assistant_id: string, status: string, required_action?: array{type: string, submit_tool_outputs: array{tool_calls: array<int, array{id: string, type: string, function: array{name: string, arguments: string}}>}}, last_error: ?array{code: string, message: string}, expires_at: ?int, started_at: ?int, cancelled_at: ?int, failed_at: ?int, completed_at: ?int, model: string, instructions: ?string, tools: array<int, array{type: string}|array{type: string}|array{type: string, function: array{description: string, name: string, parameters: array<string, mixed>}}>, metadata: array<string, string>, usage?: array{prompt_tokens: int, completion_tokens: int|null, total_tokens: int}, incomplete_details: ?array{reason: string}, temperature: float|int|null, top_p: null|float|int, max_prompt_tokens: ?int, max_completion_tokens: ?int, truncation_strategy: ?array{type: string, last_messages: ?int}, tool_choice: null|string|array{type: string, function?: array{name: string}}, response_format: null|string|array{type: string}}>
     */
    use ArrayAccessible;

    use Fakeable;
    use HasMetaInformation;

    /**
     * @param  array<int, ThreadRunResponseToolCodeInterpreter|ThreadRunResponseFileSearch|ThreadRunResponseToolFunction>  $tools
     * @param  array<string, string>  $metadata
     */
    private function __construct(
        public string $id,
        public string $object,
        public int $createdAt,
        public string $threadId,
        public string $assistantId,
        public string $status,
        public ?ThreadRunResponseRequiredAction $requiredAction,
        public ?ThreadRunResponseLastError $lastError,
        public ?int $expiresAt,
        public ?int $startedAt,
        public ?int $cancelledAt,
        public ?int $failedAt,
        public ?int $completedAt,
        public ?ThreadRunResponseIncompleteDetails $incompleteDetails,
        public string $model,
        public ?string $instructions,
        public array $tools,
        public array $metadata,
        public ?ThreadRunResponseUsage $usage,
        public ?float $temperature,
        public ?float $topP,
        public ?int $maxPromptTokens,
        public ?int $maxCompletionTokens,
        public ?ThreadRunResponseTruncationStrategy $truncationStrategy,
        public null|string|ThreadRunResponseToolChoice $toolChoice,
        public null|string|AssistantResponseResponseFormat $responseFormat,
        private readonly MetaInformation $meta,
    ) {}

    /**
     * Acts as static factory, and returns a new Response instance.
     *
     * @param  array{id: string, object: string, created_at: int, thread_id: string, assistant_id: string, status: string, required_action?: array{type: string, submit_tool_outputs: array{tool_calls: array<int, array{id: string, type: string, function: array{name: string, arguments: string}}>}}, last_error: ?array{code: string, message: string}, expires_at: ?int, started_at: ?int, cancelled_at: ?int, failed_at: ?int, completed_at: ?int, model: string, instructions: ?string, tools: array<int, array{type: 'code_interpreter'}|array{type: 'file_search'}|array{type: 'function', function: array{description: string, name: string, parameters: array<string, mixed>}}>, metadata: array<string, string>, usage?: array{prompt_tokens: int, completion_tokens: int|null, total_tokens: int}, incomplete_details: ?array{reason: string}, temperature: float|int|null, top_p: null|float|int, max_prompt_tokens: ?int, max_completion_tokens: ?int, truncation_strategy: ?array{type: string, last_messages: ?int}, tool_choice: null|string|array{type: string, function?: array{name: string}}, response_format: null|string|array{type: 'text'|'json_object'}}  $attributes
     */
    public static function from(array $attributes, MetaInformation $meta): self
    {
        $tools = array_map(
            fn (array $tool): ThreadRunResponseToolCodeInterpreter|ThreadRunResponseFileSearch|ThreadRunResponseToolFunction => match ($tool['type']) {
                'code_interpreter' => ThreadRunResponseToolCodeInterpreter::from($tool),
                'file_search' => ThreadRunResponseFileSearch::from($tool),
                'function' => ThreadRunResponseToolFunction::from($tool),
            },
            $attributes['tools'],
        );

        $responseFormat = is_array($attributes['response_format']) ?
            AssistantResponseResponseFormat::from($attributes['response_format']) :
            $attributes['response_format'];

        return new self(
            $attributes['id'],
            $attributes['object'],
            $attributes['created_at'],
            $attributes['thread_id'],
            $attributes['assistant_id'],
            $attributes['status'],
            isset($attributes['required_action']) ? ThreadRunResponseRequiredAction::from($attributes['required_action']) : null,
            isset($attributes['last_error']) ? ThreadRunResponseLastError::from($attributes['last_error']) : null,
            $attributes['expires_at'],
            $attributes['started_at'],
            $attributes['cancelled_at'],
            $attributes['failed_at'],
            $attributes['completed_at'],
            isset($attributes['incomplete_details']) ? ThreadRunResponseIncompleteDetails::from($attributes['incomplete_details']) : null,
            $attributes['model'],
            $attributes['instructions'],
            $tools,
            $attributes['metadata'],
            isset($attributes['usage']) ? ThreadRunResponseUsage::from($attributes['usage']) : null,
            $attributes['temperature'] ?? null,
            $attributes['top_p'] ?? null,
            $attributes['max_prompt_tokens'] ?? null,
            $attributes['max_completion_tokens'] ?? null,
            ($attributes['truncation_strategy'] ?? null) !== null ? ThreadRunResponseTruncationStrategy::from($attributes['truncation_strategy']) : null,
            is_array($attributes['tool_choice']) ? ThreadRunResponseToolChoice::from($attributes['tool_choice']) : $attributes['tool_choice'],
            $responseFormat,
            $meta,
        );
    }

    /**
     * {@inheritDoc}
     */
    public function toArray(): array
    {
        $data = [
            'id' => $this->id,
            'object' => $this->object,
            'created_at' => $this->createdAt,
            'assistant_id' => $this->assistantId,
            'thread_id' => $this->threadId,
            'status' => $this->status,
            'started_at' => $this->startedAt,
            'expires_at' => $this->expiresAt,
            'cancelled_at' => $this->cancelledAt,
            'failed_at' => $this->failedAt,
            'completed_at' => $this->completedAt,
            'incomplete_details' => $this->incompleteDetails?->toArray(),
            'required_action' => $this->requiredAction?->toArray(),
            'last_error' => $this->lastError?->toArray(),
            'model' => $this->model,
            'instructions' => $this->instructions,
            'tools' => array_map(
                fn (ThreadRunResponseToolCodeInterpreter|ThreadRunResponseFileSearch|ThreadRunResponseToolFunction $tool): array => $tool->toArray(),
                $this->tools,
            ),
            'metadata' => $this->metadata,
            'usage' => $this->usage?->toArray(),
            'temperature' => $this->temperature,
            'top_p' => $this->topP,
            'max_prompt_tokens' => $this->maxPromptTokens,
            'max_completion_tokens' => $this->maxCompletionTokens,
            'truncation_strategy' => $this->truncationStrategy?->toArray(),
            'tool_choice' => $this->toolChoice instanceof ThreadRunResponseToolChoice ? $this->toolChoice->toArray() : $this->toolChoice,
            'response_format' => $this->responseFormat instanceof AssistantResponseResponseFormat ? $this->responseFormat->toArray() : $this->responseFormat,
        ];

        if ($data['required_action'] === null) {
            unset($data['required_action']);
        }

        if ($data['usage'] === null) {
            unset($data['usage']);
        }

        return $data;
    }
}
