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

namespace AdvisingApp\IntegrationOpenAi\Prism\AzureOpenAi\Handlers;

use Illuminate\Http\Client\Response as ClientResponse;
use Illuminate\Support\Arr;
use Prism\Prism\Providers\OpenAI\Handlers\Text as BaseText;
use Prism\Prism\Providers\OpenAI\Maps\MessageMap;
use Prism\Prism\Providers\OpenAI\Maps\ToolCallMap;
use Prism\Prism\Providers\OpenAI\Maps\ToolChoiceMap;
use Prism\Prism\Text\Request;
use Prism\Prism\Text\Step;
use Prism\Prism\ValueObjects\Meta;
use Prism\Prism\ValueObjects\ToolResult;
use Prism\Prism\ValueObjects\Usage;

class Text extends BaseText
{
    /**
     * @param  array<string, mixed>  $data
     * @param  ToolResult[]  $toolResults
     */
    protected function addStep(array $data, Request $request, ClientResponse $clientResponse, array $toolResults = []): void
    {
        $this->responseBuilder->addStep(new Step(
            text: data_get($data, 'output.{last}.content.0.text') ?? '',
            finishReason: $this->mapFinishReason($data),
            toolCalls: ToolCallMap::map(array_filter(data_get($data, 'output', []), fn (array $output): bool => $output['type'] === 'function_call')),
            toolResults: $toolResults,
            usage: new Usage(
                promptTokens: data_get($data, 'usage.input_tokens', 0) - data_get($data, 'usage.input_tokens_details.cached_tokens', 0),
                completionTokens: data_get($data, 'usage.output_tokens'),
                cacheReadInputTokens: data_get($data, 'usage.input_tokens_details.cached_tokens'),
                thoughtTokens: data_get($data, 'usage.output_token_details.reasoning_tokens'),
            ),
            meta: new Meta(
                id: data_get($data, 'id'),
                model: data_get($data, 'model'),
            ),
            messages: $request->messages(),
            additionalContent: [
                'generated_images' => array_filter($data['output'] ?? [], fn (array $output): bool => $output['type'] === 'image_generation_call'),
            ],
            systemPrompts: $request->systemPrompts(),
        ));
    }

    protected function sendRequest(Request $request): ClientResponse
    {
        return $this->client->post(
            'responses',
            array_merge([
                'model' => $request->model(),
                'input' => (new MessageMap($request->messages(), $request->systemPrompts()))(),
                'max_output_tokens' => $request->maxTokens(),
            ], Arr::whereNotNull([
                'temperature' => $request->temperature(),
                'top_p' => $request->topP(),
                'metadata' => $request->providerOptions('metadata'),
                // 'tools' => $this->buildTools($request),
                // 'tool_choice' => ToolChoiceMap::map($request->toolChoice()),
                'previous_response_id' => $request->providerOptions('previous_response_id'),
                'truncation' => $request->providerOptions('truncation'),
                'tools' => $request->providerOptions('tools'),
                'tool_choice' => $request->providerOptions('tool_choice'),
            ]))
        );
    }
}
