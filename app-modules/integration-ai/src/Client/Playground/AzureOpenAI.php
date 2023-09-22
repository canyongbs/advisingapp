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
        $fakeText = $this->generateFakeText();

        $handle = fopen('php://memory', 'r+');
        fwrite($handle, $fakeText);
        rewind($handle);

        $this->client = new ClientFake([
            CreateStreamedResponse::fake(fread($handle, strlen($fakeText))),
        ]);

        fclose($handle);
    }

    protected function generateFakeText(): string
    {
        return resolve(MockStreamResponseGenerator::class)
            // You can chain on either of the following methods to simulate an error from the API
            // ->withLengthError()
            // ->withContentFilterError()
            ->generateFakeStreamResponse();
    }

    protected function generateStreamedResponse(StreamResponse $stream, Closure $callback): string
    {
        $fullResponse = '';

        foreach ($stream as $response) {
            // artificial delay to make the playground client seem more realistic
            sleep(1);

            $streamedContent = $this->shouldSendResponse($response);

            if (! is_null($streamedContent)) {
                $callback($streamedContent);

                $fullResponse .= $streamedContent;
            }
        }

        return $fullResponse;
    }
}
