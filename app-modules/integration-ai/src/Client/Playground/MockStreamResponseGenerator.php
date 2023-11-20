<?php

/*
<COPYRIGHT>

Copyright © 2022-2023, Canyon GBS LLC

All rights reserved.

This file is part of a project developed using Laravel, which is an open-source framework for PHP.
Canyon GBS LLC acknowledges and respects the copyright of Laravel and other open-source
projects used in the development of this solution.

This project is licensed under the Affero General Public License (AGPL) 3.0.
For more details, see https://github.com/canyongbs/assistbycanyongbs/blob/main/LICENSE.

Notice:
- The copyright notice in this file and across all files and applications in this
 repository cannot be removed or altered without violating the terms of the AGPL 3.0 License.
- The software solution, including services, infrastructure, and code, is offered as a
 Software as a Service (SaaS) by Canyon GBS LLC.
- Use of this software implies agreement to the license terms and conditions as stated
 in the AGPL 3.0 License.

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
