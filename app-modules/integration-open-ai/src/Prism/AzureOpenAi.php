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

namespace AdvisingApp\IntegrationOpenAi\Prism;

use AdvisingApp\IntegrationOpenAi\Prism\AzureOpenAi\Handlers\Stream;
use AdvisingApp\IntegrationOpenAi\Prism\AzureOpenAi\Handlers\Structured;
use App\Features\OpenAiResponsesApiSettingsFeature;
use Generator;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Arr;
use Override;
use Prism\Prism\Providers\OpenAI\OpenAI;
use Prism\Prism\Structured\Request as StructuredRequest;
use Prism\Prism\Structured\Response as StructuredResponse;
use Prism\Prism\Text\Request as TextRequest;

class AzureOpenAi extends OpenAI
{
    public function __construct() {}

    #[Override]
    public function stream(TextRequest $request): Generator
    {
        $handler = new Stream($this->client(
            $request->clientOptions(),
            $request->clientRetry()
        ));

        return $handler->handle($request);
    }

    #[Override]
    public function structured(StructuredRequest $request): StructuredResponse
    {
        $handler = new Structured($this->client(
            $request->clientOptions(),
            $request->clientRetry()
        ));

        return $handler->handle($request);
    }

    /**
     * @param  array<string, mixed>  $options
     * @param  array<mixed>  $retry
     */
    protected function client(array $options = [], array $retry = [], ?string $baseUrl = null): PendingRequest
    {
        return $this->baseClient()
            ->withHeaders([
                'api-key' => $options['apiKey'],
                ...$options['headers'] ?? [],
            ])
            ->withQueryParameters(['api-version' => $options['apiVersion']])
            ->withOptions(Arr::except($options, ['apiKey', 'apiVersion', 'deployment', 'headers']))
            ->when($retry !== [], fn ($client) => $client->retry(...$retry))
            ->baseUrl(OpenAiResponsesApiSettingsFeature::active() ? "{$options['deployment']}/v1" : $options['deployment']);
    }
}
