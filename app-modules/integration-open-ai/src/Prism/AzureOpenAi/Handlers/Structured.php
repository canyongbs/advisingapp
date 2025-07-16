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

use Illuminate\Http\Client\Response;
use Illuminate\Support\Arr;
use Prism\Prism\Exceptions\PrismException;
use Prism\Prism\Providers\OpenAI\Handlers\Structured as BaseStructured;
use Prism\Prism\Providers\OpenAI\Maps\MessageMap;
use Prism\Prism\Structured\Request;
use Throwable;

class Structured extends BaseStructured
{
    /**
     * @param  array{type: 'json_schema', name: string, schema: array<mixed>, strict?: bool}|array{type: 'json_object'}  $responseFormat
     */
    protected function sendRequest(Request $request, array $responseFormat): Response
    {
        try {
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
                    'previous_response_id' => $request->providerOptions('previous_response_id'),
                    'truncation' => $request->providerOptions('truncation'),
                    'text' => [
                        'format' => $responseFormat,
                    ],
                    'tools' => $request->providerOptions('tools'),
                    'tool_choice' => $request->providerOptions('tool_choice'),
                ]))
            );
        } catch (Throwable $e) {
            throw PrismException::providerRequestError($request->model(), $e);
        }
    }
}
