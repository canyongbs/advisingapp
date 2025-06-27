<?php

namespace AdvisingApp\IntegrationOpenAi\Prism\AzureOpenAi\Handlers;

use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Arr;
use Prism\Prism\Exceptions\PrismException;
use Prism\Prism\Exceptions\PrismRateLimitedException;
use Prism\Prism\Providers\OpenAI\Handlers\Stream as BaseStream;
use Prism\Prism\Providers\OpenAI\Maps\MessageMap;
use Prism\Prism\Providers\OpenAI\Maps\ToolChoiceMap;
use Prism\Prism\Providers\OpenAI\Maps\ToolMap;
use Prism\Prism\Text\Request;
use Throwable;

class Stream extends BaseStream
{
    protected function sendRequest(Request $request): Response
    {
        try {
            return $this
                ->client
                ->withOptions(['stream' => true])
                ->throw()
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
                        // 'tools' => ToolMap::map($request->tools()),
                        // 'tool_choice' => ToolChoiceMap::map($request->toolChoice()),
                        'previous_response_id' => $request->providerOptions('previous_response_id'),
                        'truncation' => $request->providerOptions('truncation'),
                        'reasoning' => $request->providerOptions('reasoning'),
                        'tools' => $request->providerOptions('tools'),
                        'tool_choice' => $request->providerOptions('tool_choice'),
                    ]))
                );
        } catch (Throwable $e) {
            if ($e instanceof RequestException && $e->response->getStatusCode() === 429) {
                throw new PrismRateLimitedException($this->processRateLimits($e->response));
            }

            throw PrismException::providerRequestError($request->model(), $e);
        }
    }
}
