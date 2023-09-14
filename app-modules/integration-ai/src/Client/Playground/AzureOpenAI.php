<?php

namespace Assist\IntegrationAI\Client\Playground;

use Closure;
use OpenAI\Testing\ClientFake;
use OpenAI\Responses\StreamResponse;
use Assist\IntegrationAI\Client\BaseAIChatClient;
use OpenAI\Responses\Chat\CreateStreamedResponse;

class AzureOpenAI extends BaseAIChatClient
{
    protected function initializeClient(): void
    {
        $fakeText = resolve(MockStreamResponseGenerator::class)->generateFakeStreamResponse();

        $handle = fopen('php://memory', 'r+');
        fwrite($handle, $fakeText);
        rewind($handle);

        $this->client = new ClientFake([
            CreateStreamedResponse::fake(fread($handle, strlen($fakeText))),
        ]);

        fclose($handle);
    }

    protected function generateStreamedResponse(StreamResponse $stream, Closure $callback): string
    {
        $fullResponse = '';

        foreach ($stream as $response) {
            sleep(1);
            $streamedContent = $this->shouldSendResponse($response);

            if (! is_null($callback)) {
                if (! is_null($streamedContent)) {
                    $callback($streamedContent);
                }
            }

            $fullResponse .= $streamedContent;
        }

        return $fullResponse;
    }
}
