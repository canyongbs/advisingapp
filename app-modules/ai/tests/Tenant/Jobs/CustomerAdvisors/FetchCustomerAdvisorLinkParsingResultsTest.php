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

use AdvisingApp\Ai\Jobs\CustomerAdvisors\FetchCustomerAdvisorLinkParsingResults;
use AdvisingApp\Ai\Jobs\SubmitAiLinkForParsing;
use AdvisingApp\Ai\Models\CustomerAdvisorLink;
use AdvisingApp\Ai\Settings\AiIntegrationsSettings;
use App\Features\LlamaParseLinkParsingFeature;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Queue;

// TODO: Cleanup Task (llamaparse-link-parsing): delete this file along with the `FetchCustomerAdvisorLinkParsingResults` job.

it('refreshes existing parsing results when explicitly requested', function (): void {
    LlamaParseLinkParsingFeature::deactivate();

    Http::fake([
        'https://r.jina.ai/*' => Http::response('fresh parsing results', 200),
    ]);

    $settings = app(AiIntegrationsSettings::class);
    $settings->jina_deepsearch_v1_api_key = 'test-api-key';

    $link = CustomerAdvisorLink::factory()->create([
        'parsing_results' => 'stale parsing results',
    ]);

    (new FetchCustomerAdvisorLinkParsingResults($link, refreshExistingParsingResults: true))->handle();

    $link->refresh();

    expect($link->parsing_results)->toBe('fresh parsing results');
});

it('does not refresh existing parsing results when not explicitly requested', function (): void {
    LlamaParseLinkParsingFeature::deactivate();

    Http::fake([
        'https://r.jina.ai/*' => Http::response('fresh parsing results', 200),
    ]);

    $settings = app(AiIntegrationsSettings::class);
    $settings->jina_deepsearch_v1_api_key = 'test-api-key';

    $link = CustomerAdvisorLink::factory()->create([
        'parsing_results' => 'stale parsing results',
    ]);

    (new FetchCustomerAdvisorLinkParsingResults($link, refreshExistingParsingResults: false))->handle();

    $link->refresh();

    expect($link->parsing_results)->toBe('stale parsing results');
});

it('hands the link over to LlamaParse instead of parsing it once LlamaParse link parsing is active', function (?string $parsingResults, bool $refreshExistingParsingResults): void {
    Queue::fake();
    Http::fake();

    $link = CustomerAdvisorLink::factory()->create([
        'parsing_results' => $parsingResults,
        'created_at' => now()->subHour()->subMinute(),
    ]);

    (new FetchCustomerAdvisorLinkParsingResults($link, $refreshExistingParsingResults))->handle();

    Http::assertNothingSent();

    Queue::assertPushed(SubmitAiLinkForParsing::class, fn (SubmitAiLinkForParsing $job): bool => ($job->uniqueId() === $link->getKey())
        && ($job->refreshesExistingParsingResults() === $refreshExistingParsingResults));

    expect($link->refresh()->parsing_results)->toBe($parsingResults);
})->with([
    'unparsed link' => [null, false],
    'Keep Current refresh' => ['Existing parsing results', true],
]);
