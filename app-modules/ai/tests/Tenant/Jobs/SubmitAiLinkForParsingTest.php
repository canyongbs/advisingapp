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
use AdvisingApp\Ai\Jobs\FetchAiFilesParsingResults;
use AdvisingApp\Ai\Jobs\SubmitAiLinkForParsing;
use AdvisingApp\Ai\Models\AiAssistantLink;
use AdvisingApp\Ai\Models\CustomerAdvisorLink;
use AdvisingApp\Ai\Services\LlamaParse\LlamaParseV2Client;
use AdvisingApp\Ai\Settings\AiIntegrationsSettings;
use Illuminate\Http\Client\Request;
use Illuminate\Http\Client\RequestException;
use Illuminate\Pipeline\Pipeline;
use Illuminate\Queue\Jobs\FakeJob;
use Illuminate\Queue\Middleware\WithoutOverlapping;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Exceptions;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Queue;

use function Pest\Laravel\travelTo;

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

it('does not submit a link that was deleted after this was queued', function (bool $refreshExistingParsingResults): void {
    Http::fake();

    $link = CustomerAdvisorLink::factory()->create(['parsing_results' => $refreshExistingParsingResults ? 'Existing parsing results' : null]);

    $job = new SubmitAiLinkForParsing($link, $refreshExistingParsingResults);

    $link->delete();

    $job->handle(app(LlamaParseV2Client::class));

    Http::assertNothingSent();

    expect($link->refresh()->parsing_job_id)->toBeNull();
})->with([
    'new link' => [false],
    'Keep Current refresh' => [true],
]);

it('marks a link as failed without submitting it once it could not be submitted within an hour', function (): void {
    Exceptions::fake();
    Http::fake();

    $link = CustomerAdvisorLink::factory()->create([
        'parsing_results' => null,
        'parsing_last_checked_at' => now()->subMinutes(3),
        'created_at' => now()->subHour()->subMinute(),
    ]);

    (new SubmitAiLinkForParsing($link))->handle(app(LlamaParseV2Client::class));

    expect($link->refresh()->parsing_failed_at)->not->toBeNull()
        ->and($link->parsing_job_id)->toBeNull();

    Http::assertNothingSent();
    Exceptions::assertReported(AiLinkParsingFailedException::class);
});

it('submits a link that was never tried, even after its first hour', function (): void {
    Http::fake([
        'https://api.cloud.llamaindex.ai/api/v2/parse' => Http::response(['id' => 'pjb-123']),
    ]);

    $link = CustomerAdvisorLink::factory()->create([
        'parsing_results' => null,
        'created_at' => now()->subHour()->subMinute(),
    ]);

    expect($link->parsing_last_checked_at)->toBeNull();

    (new SubmitAiLinkForParsing($link))->handle(app(LlamaParseV2Client::class));

    $link->refresh();

    expect($link->parsing_job_id)->toBe('pjb-123')
        ->and($link->parsing_failed_at)->toBeNull();
});

it('does not mark a link as failed while its last submission within the hour is still in progress', function (): void {
    Exceptions::fake();

    $link = CustomerAdvisorLink::factory()->create([
        'parsing_results' => null,
        'parsing_last_checked_at' => now()->subMinutes(5),
        'created_at' => now()->subMinutes(59),
    ]);

    Http::fake([
        'https://api.cloud.llamaindex.ai/api/v2/parse' => function () {
            travelTo(now()->addMinutes(2));

            (new FetchAiFilesParsingResults())->handle();

            return Http::response(['id' => 'pjb-123']);
        },
    ]);

    (new SubmitAiLinkForParsing($link))->handle(app(LlamaParseV2Client::class));

    $link->refresh();

    expect($link->parsing_job_id)->toBe('pjb-123')
        ->and($link->parsing_failed_at)->toBeNull();

    Exceptions::assertNotReported(AiLinkParsingFailedException::class);
});

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

it('does not submit a link that another submission started a parse job for after this one was queued', function (): void {
    Http::fake();

    $link = CustomerAdvisorLink::factory()->create(['parsing_results' => null]);

    $job = new SubmitAiLinkForParsing($link);

    CustomerAdvisorLink::query()->whereKey($link->getKey())->update([
        'parsing_job_id' => 'pjb-other',
        'parsing_started_at' => now(),
    ]);

    $job->handle(app(LlamaParseV2Client::class));

    Http::assertNothingSent();

    expect($link->refresh()->parsing_job_id)->toBe('pjb-other');
});

it('does not submit a link while another submission of it is running', function (): void {
    Http::fake();

    $link = CustomerAdvisorLink::factory()->create(['parsing_results' => null]);

    $job = new SubmitAiLinkForParsing($link);

    $middleware = $job->middleware();

    assert($middleware[0] instanceof WithoutOverlapping);

    $runningSubmissionLock = Cache::lock($middleware[0]->getLockKey($job), 120);

    expect($runningSubmissionLock->get())->toBeTrue();

    (new Pipeline(app()))
        ->send($job)
        ->through($middleware)
        ->then(fn (SubmitAiLinkForParsing $job) => $job->handle(app(LlamaParseV2Client::class)));

    $runningSubmissionLock->release();

    Http::assertNothingSent();

    expect($link->refresh()->parsing_job_id)->toBeNull();
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

it('leaves a link that is still being submitted for the first time to the every-minute sweep when asked to refresh it', function (Closure $applyState): void {
    Http::fake();

    $link = $applyState(CustomerAdvisorLink::factory())->create(['parsing_results' => null]);

    $job = (new SubmitAiLinkForParsing($link, refreshExistingParsingResults: true))->withFakeQueueInteractions();

    $job->handle(app(LlamaParseV2Client::class));

    Http::assertNothingSent();
    $job->assertNotReleased();

    $link->refresh();

    expect($link->parsing_job_id)->toBeNull()
        ->and($link->parsing_failed_at)->toBeNull();
})->with([
    'already tried' => [fn (CustomerAdvisorLinkFactory $factory): CustomerAdvisorLinkFactory => $factory->state([
        'created_at' => now()->subMinutes(50),
        'parsing_last_checked_at' => now()->subMinutes(3),
    ])],
    'not tried yet' => [fn (CustomerAdvisorLinkFactory $factory): CustomerAdvisorLinkFactory => $factory->state([
        'created_at' => now()->subMinute(),
    ])],
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
