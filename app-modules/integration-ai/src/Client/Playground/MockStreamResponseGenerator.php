<?php

/*
<COPYRIGHT>

    Copyright © 2022-2023, Canyon GBS LLC. All rights reserved.

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

namespace Assist\IntegrationAI\Client\Playground;

use Illuminate\Support\Str;

class MockStreamResponseGenerator
{
    protected $minChunkSize = 2;

    protected $maxChunkSize = 4;

    protected $terminateWithLength = false;

    protected $terminateWithContentFilter = false;

    public function generateFakeStreamResponse(string $type = 'paragraph', int $quantity = 2): string
    {
        $contents = $this->generateFakeContent($type, $quantity);
        $streamedContents = $this->prepareForStreaming($contents, $type);

        $chatId = 'chatcmpl-' . Str::random(8);

        $responses = [];
        $responses[] = $this->initChat($chatId);

        foreach ($streamedContents as $content) {
            $responses[] = $this->addContentToChat($chatId, $content);
        }

        match (true) {
            $this->terminateWithLength => $responses[] = $this->terminateChatWithLengthFinishReason($chatId),
            $this->terminateWithContentFilter => $responses[] = $this->terminateChatWithContentFilterFinishReason($chatId),
            default => $responses[] = $this->terminateChat($chatId),
        };

        $responses[] = "data: [DONE]\n";

        return implode('', $responses);
    }

    public function withLengthError(): self
    {
        $this->terminateWithLength = true;

        return $this;
    }

    public function withContentFilterError(): self
    {
        $this->terminateWithContentFilter = true;

        return $this;
    }

    protected function generateFakeContent(string $type, int $quantity): array
    {
        $contents = [];

        for ($i = 0; $i < $quantity; $i++) {
            match ($type) {
                'sentence' => $contents[] = fake()->sentence,
                'paragraph' => $contents[] = fake()->paragraph,
                default => throw new \InvalidArgumentException('Unsupported type for fake content.'),
            };
        }

        return $contents;
    }

    protected function prepareForStreaming(array $contents, string $type): array
    {
        $streamedContents = [];

        foreach ($contents as $content) {
            if ($type === 'sentence') {
                $words = preg_split('/\s+/', $content);

                while (count($words) > 0) {
                    $chunkSize = rand($this->minChunkSize, min($this->maxChunkSize, count($words)));
                    $streamedContents[] = implode(' ', array_splice($words, 0, $chunkSize));
                }
            } elseif ($type === 'paragraph') {
                $sentences = preg_split('/(?<=[.!?])\s+/', $content);

                while (count($sentences) > 0) {
                    $chunkSize = rand($this->minChunkSize, min($this->maxChunkSize, count($sentences)));
                    $streamedContents[] = implode(' ', array_splice($sentences, 0, $chunkSize));
                }
            }
        }

        return $streamedContents;
    }

    protected function addContentToChat(string $chatId, string $content): string
    {
        return 'data: ' . json_encode([
            'id' => $chatId,
            'object' => 'chat.completion.chunk',
            'created' => time(),
            'model' => 'canyon-test-model',
            'choices' => [
                [
                    'delta' => [
                        'content' => ' ' . $content,
                    ],
                    'index' => 0,
                    'finish_reason' => null,
                ],
            ],
        ]) . "\n";
    }

    protected function initChat(string $chatId): string
    {
        return 'data: ' . json_encode([
            'id' => $chatId,
            'object' => 'chat.completion.chunk',
            'created' => time(),
            'model' => 'canyon-test-model',
            'choices' => [
                [
                    'delta' => [
                        'role' => 'assistant',
                    ],
                    'index' => 0,
                    'finish_reason' => null,
                ],
            ],
        ]) . "\n";
    }

    protected function terminateChat(string $chatId): string
    {
        return 'data: ' . json_encode([
            'id' => $chatId,
            'object' => 'chat.completion.chunk',
            'created' => time(),
            'model' => 'canyon-test-model',
            'choices' => [
                [
                    'delta' => [],
                    'index' => 0,
                    'finish_reason' => 'stop',
                ],
            ],
        ]) . "\n";
    }

    protected function terminateChatWithLengthFinishReason(string $chatId): string
    {
        return 'data: ' . json_encode([
            'id' => $chatId,
            'object' => 'chat.completion.chunk',
            'created' => time(),
            'model' => 'canyon-test-model',
            'choices' => [
                [
                    'delta' => [],
                    'index' => 0,
                    'finish_reason' => 'length',
                ],
            ],
        ]) . "\n";
    }

    protected function terminateChatWithContentFilterFinishReason(string $chatId): string
    {
        return 'data: ' . json_encode([
            'id' => $chatId,
            'object' => 'chat.completion.chunk',
            'created' => time(),
            'model' => 'canyon-test-model',
            'choices' => [
                [
                    'delta' => [],
                    'index' => 0,
                    'finish_reason' => 'content_filter',
                ],
            ],
        ]) . "\n";
    }
}
