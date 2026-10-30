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

use AdvisingApp\Ai\Database\Factories\CustomerAdvisorLinkFactory;
use AdvisingApp\Ai\Exceptions\AiLinkParsingFailedException;
use AdvisingApp\Ai\Exceptions\LlamaParseApiKeyNotConfiguredException;
use AdvisingApp\Ai\Jobs\SubmitAiLinkForParsing;
use AdvisingApp\Ai\Models\AiAssistantLink;
use AdvisingApp\Ai\Models\CustomerAdvisorLink;
use AdvisingApp\Ai\Services\LlamaParse\LlamaParseV2Client;
use AdvisingApp\Ai\Settings\AiIntegrationsSettings;
use Illuminate\Http\Client\Request;
use Illuminate\Http\Client\RequestException;
use Illuminate\Queue\Jobs\FakeJob;
use Illuminate\Support\Facades\Exceptions;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Queue;

beforeEach(function (): void {
    Queue::fake();
    Http::preventStrayRequests();

    $settings = app(AiIntegrationsSettings::class);
    $settings->llamaparse_api_key = 'test-api-key';
    $settings->save();
});

it('submits the link to LlamaParse and stores the parse job', function (string $linkModel): void {
    Http::fake([
        'https://api.cloud.llamaindex.ai/api/v2/parse' => Http::response(['id' => 'pjb-123']),
    ]);

    $link = $linkModel::factory()->create(['parsing_results' => null]);

    expect($link->parsing_job_id)->toBeNull();

    (new SubmitAiLinkForParsing($link))->handle(app(LlamaParseV2Client::class));

    $link->refresh();

    expect($link->parsing_job_id)->toBe('pjb-123')
        ->and($link->parsing_started_at)->not->toBeNull()
        ->and($link->parsing_last_checked_at)->not->toBeNull()
        ->and($link->parsing_failed_at)->toBeNull();

    Http::assertSent(fn (Request $request): bool => $request->data() === [
        'source_url' => $link->url,
        'disable_cache' => true,
        'tier' => 'cost_effective',
        'version' => LlamaParseV2Client::VERSION,
    ]);
})->with([
    'customer advisor link' => [CustomerAdvisorLink::class],
    'AI assistant link' => [AiAssistantLink::class],
]);

it('does not change the `updated_at` of the link', function (): void {
    Http::fake([
        'https://api.cloud.llamaindex.ai/api/v2/parse' => Http::response(['id' => 'pjb-123']),
    ]);

    $link = CustomerAdvisorLink::factory()->create([
        'parsing_results' => null,
        'updated_at' => now()->subDay(),
    ]);

    $updatedAt = $link->updated_at;

    (new SubmitAiLinkForParsing($link))->handle(app(LlamaParseV2Client::class));

    expect($link->refresh()->parsing_job_id)->toBe('pjb-123')
        ->and($link->updated_at->toDateTimeString())->toBe($updatedAt->toDateTimeString());
});

it('does not submit a link that already has a parse job in progress', function (): void {
    Http::fake();

    $link = CustomerAdvisorLink::factory()->parsingInProgress()->create([
        'parsing_results' => 'Existing parsing results',
    ]);

    $parsingJobId = $link->parsing_job_id;

    (new SubmitAiLinkForParsing($link, refreshExistingParsingResults: true))->handle(app(LlamaParseV2Client::class));

    Http::assertNothingSent();

    expect($link->refresh()->parsing_job_id)->toBe($parsingJobId);
});

it('does not submit a link that is already parsed or has failed unless it is being refreshed', function (Closure $applyState): void {
    Http::fake();

    $link = $applyState(CustomerAdvisorLink::factory())->create();

    (new SubmitAiLinkForParsing($link))->handle(app(LlamaParseV2Client::class));

    Http::assertNothingSent();

    expect($link->refresh()->parsing_job_id)->toBeNull();
})->with([
    'parsed' => [fn (CustomerAdvisorLinkFactory $factory): CustomerAdvisorLinkFactory => $factory->state(['parsing_results' => 'Existing parsing results'])],
    'failed' => [fn (CustomerAdvisorLinkFactory $factory): CustomerAdvisorLinkFactory => $factory->parsingFailed()],
]);

it('submits a parsed link that is being refreshed, keeping its existing parsing results', function (): void {
    Http::fake([
        'https://api.cloud.llamaindex.ai/api/v2/parse' => Http::response(['id' => 'pjb-123']),
    ]);

    $link = CustomerAdvisorLink::factory()->parsingFailed()->create([
        'parsing_results' => 'Existing parsing results',
    ]);

    (new SubmitAiLinkForParsing($link, refreshExistingParsingResults: true))->handle(app(LlamaParseV2Client::class));

    $link->refresh();

    expect($link->parsing_job_id)->toBe('pjb-123')
        ->and($link->parsing_results)->toBe('Existing parsing results')
        ->and($link->parsing_failed_at)->toBeNull();
});

it('reports an error response and leaves the link to be submitted again', function (): void {
    Exceptions::fake();

    Http::fake([
        'https://api.cloud.llamaindex.ai/api/v2/parse' => Http::response(['detail' => 'Internal server error'], 500),
    ]);

    $link = CustomerAdvisorLink::factory()->create(['parsing_results' => null]);

    $job = (new SubmitAiLinkForParsing($link))->withFakeQueueInteractions();

    $job->handle(app(LlamaParseV2Client::class));

    Exceptions::assertReported(RequestException::class);

    // New links are submitted again by the every-minute sweep, not by releasing this job.
    $job->assertNotReleased();

    $link->refresh();

    expect($link->parsing_job_id)->toBeNull()
        ->and($link->parsing_failed_at)->toBeNull()
        ->and($link->parsing_last_checked_at)->not->toBeNull();
});

it('reports a missing API key without sending a request', function (): void {
    Exceptions::fake();
    Http::fake();

    $settings = app(AiIntegrationsSettings::class);
    $settings->llamaparse_api_key = null;
    $settings->save();

    $link = CustomerAdvisorLink::factory()->create(['parsing_results' => null]);

    (new SubmitAiLinkForParsing($link))->handle(app(LlamaParseV2Client::class));

    Exceptions::assertReported(LlamaParseApiKeyNotConfiguredException::class);
    Http::assertNothingSent();

    expect($link->refresh()->parsing_job_id)->toBeNull();
});

it('retries a refresh that cannot be submitted, keeping its existing parsing results', function (int $attempt, int $retryDelayInSeconds): void {
    Exceptions::fake();

    Http::fake([
        'https://api.cloud.llamaindex.ai/api/v2/parse' => Http::response(['detail' => 'Internal server error'], 500),
    ]);

    $link = CustomerAdvisorLink::factory()->create(['parsing_results' => 'Existing parsing results']);

    $job = (new SubmitAiLinkForParsing($link, refreshExistingParsingResults: true))->withFakeQueueInteractions();

    assert($job->job instanceof FakeJob);
    $job->job->attempts = $attempt;

    $job->handle(app(LlamaParseV2Client::class));

    Exceptions::assertReported(RequestException::class);
    $job->assertReleased(delay: $retryDelayInSeconds);

    $link->refresh();

    expect($link->parsing_results)->toBe('Existing parsing results')
        ->and($link->parsing_failed_at)->toBeNull();
})->with([
    'first attempt' => [1, 300],
    'second attempt' => [2, 900],
]);

it('marks a refresh as failed after its last attempt, keeping its existing parsing results', function (): void {
    Exceptions::fake();

    Http::fake([
        'https://api.cloud.llamaindex.ai/api/v2/parse' => Http::response(['detail' => 'Internal server error'], 500),
    ]);

    $link = CustomerAdvisorLink::factory()->create(['parsing_results' => 'Existing parsing results']);

    $job = (new SubmitAiLinkForParsing($link, refreshExistingParsingResults: true))->withFakeQueueInteractions();

    assert($job->job instanceof FakeJob);
    $job->job->attempts = $job->tries;

    expect($link->parsing_failed_at)->toBeNull();

    $job->handle(app(LlamaParseV2Client::class));

    $job->assertNotReleased();

    Exceptions::assertReported(AiLinkParsingFailedException::class);

    $link->refresh();

    expect($link->parsing_failed_at)->not->toBeNull()
        ->and($link->parsing_results)->toBe('Existing parsing results');
});
