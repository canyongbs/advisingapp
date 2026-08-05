<?php

/*
<COPYRIGHT>

    Copyright © 2016-2026, Canyon GBS Inc. All rights reserved.

    Advising App® is licensed under the Elastic License 2.0. For more details,
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
    - Canyon GBS Inc. respects the intellectual property rights of others and expects the
      same in return. Canyon GBS® and Advising App® are registered trademarks of
      Canyon GBS Inc., and we are committed to enforcing and protecting our trademarks
      vigorously.
    - The software solution, including services, infrastructure, and code, is offered as a
      Software as a Service (SaaS) by Canyon GBS Inc.
    - Use of this software implies agreement to the license terms and conditions as stated
      in the Elastic License 2.0.

    For more information or inquiries please visit our website at
    https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

namespace AdvisingApp\IntegrationOpenAi\Prism\AzureOpenAi\Handlers;

use AdvisingApp\IntegrationOpenAi\Prism\AzureOpenAi\Maps\MessageMap;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Arr;
use Prism\Prism\Exceptions\PrismRateLimitedException;
use Prism\Prism\Providers\OpenAI\Handlers\Stream as BaseStream;
use Prism\Prism\Providers\OpenAI\Maps\ToolChoiceMap;
use Prism\Prism\Text\Request;
use Psr\Http\Message\StreamInterface;

class Stream extends BaseStream
{
    protected function sendRequest(Request $request): Response
    {
        return $this
            ->client
            ->withOptions(['stream' => true])
            ->post(
                'responses',
                array_merge([
                    'stream' => true,
                    'model' => $request->model(),
                    'input' => (new MessageMap($request->messages(), $request->systemPrompts()))(),
                    'max_output_tokens' => $request->maxTokens(),
                ], Arr::whereNotNull([
                    'temperature' => $request->temperature(),
                    'top_p' => $request->topP(),
                    'metadata' => $request->providerOptions('metadata'),
                    // 'tools' => $this->buildTools($request),
                    // 'tool_choice' => ToolChoiceMap::map($request->toolChoice()),
                    'instructions' => $request->providerOptions('instructions'),
                    'previous_response_id' => $request->providerOptions('previous_response_id'),
                    'truncation' => $request->providerOptions('truncation'),
                    'reasoning' => $request->providerOptions('reasoning'),
                    'tools' => $request->providerOptions('tools'),
                    'tool_choice' => $request->providerOptions('tool_choice'),
                ]))
            );
    }

    /**
     * @return array<string, mixed>|null
     */
    protected function parseNextDataLine(StreamInterface $stream): ?array
    {
        $data = parent::parseNextDataLine($stream);

        // Prism discards the provider's message when it throws for a rate limit, so we capture the retry delay here first.
        if (is_array($data) && data_get($data, 'error.code') === 'rate_limit_exceeded') {
            throw new PrismRateLimitedException([], $this->extractRetryAfterSeconds(data_get($data, 'error.message')));
        }

        return $data;
    }

    private function extractRetryAfterSeconds(mixed $message): ?int
    {
        if (! is_string($message) || blank($message)) {
            return null;
        }

        // Azure phrasing, e.g. "Please retry after 26 seconds."
        if (preg_match('/retry after (\d+)\s*second/i', $message, $matches)) {
            return max(1, (int) $matches[1]);
        }

        // OpenAI phrasing, e.g. "Please try again in 1.5s" or "2m30s" or "200ms".
        if (preg_match('/try again in\s+([0-9hms.\s]+)/i', $message, $matches)) {
            return $this->sumDurationToSeconds($matches[1]);
        }

        return null;
    }

    private function sumDurationToSeconds(string $duration): ?int
    {
        if (preg_match_all('/(\d+(?:\.\d+)?)\s*(ms|h|m|s)/i', $duration, $matches, PREG_SET_ORDER) === 0) {
            return null;
        }

        $seconds = 0.0;

        foreach ($matches as $match) {
            $seconds += match (strtolower($match[2])) {
                'ms' => ((float) $match[1]) / 1000,
                'm' => ((float) $match[1]) * 60,
                'h' => ((float) $match[1]) * 3600,
                default => (float) $match[1],
            };
        }

        return max(1, (int) ceil($seconds));
    }
}
