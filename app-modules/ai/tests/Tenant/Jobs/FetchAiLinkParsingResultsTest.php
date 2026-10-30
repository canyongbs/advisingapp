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

use AdvisingApp\Ai\Exceptions\AiLinkParsingFailedException;
use AdvisingApp\Ai\Jobs\FetchAiLinkParsingResults;
use AdvisingApp\Ai\Models\AiAssistantLink;
use AdvisingApp\Ai\Models\CustomerAdvisorLink;
use AdvisingApp\Ai\Services\LlamaParse\LlamaParseV2Client;
use AdvisingApp\Ai\Settings\AiIntegrationsSettings;
use Illuminate\Http\Client\Request;
use Illuminate\Http\Client\RequestException;
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

it('stores the parsing results of a completed parse job', function (string $linkModel): void {
    Http::fake([
        'https://api.cloud.llamaindex.ai/api/v2/parse/*' => Http::response([
            'job' => ['id' => 'pjb-123', 'status' => 'COMPLETED'],
            'markdown_full' => '# Course Schedule',
        ]),
    ]);

    $link = $linkModel::factory()->parsingInProgress()->create([
        'parsing_results' => null,
        'updated_at' => now()->subDay(),
    ]);

    $parsingJobId = $link->parsing_job_id;
    $updatedAt = $link->updated_at;

    (new FetchAiLinkParsingResults($link, $parsingJobId))->handle(app(LlamaParseV2Client::class));

    $link->refresh();

    expect($link->parsing_results)->toBe('# Course Schedule')
        ->and($link->parsing_job_id)->toBeNull()
        ->and($link->parsing_failed_at)->toBeNull()
        ->and($link->updated_at->isAfter($updatedAt))->toBeTrue();

    Http::assertSent(fn (Request $request): bool => $request->url() === "https://api.cloud.llamaindex.ai/api/v2/parse/{$parsingJobId}?expand=markdown_full");
})->with([
    'customer advisor link' => [CustomerAdvisorLink::class],
    'AI assistant link' => [AiAssistantLink::class],
]);

it('replaces the existing parsing results once a refresh completes', function (): void {
    Http::fake([
        'https://api.cloud.llamaindex.ai/api/v2/parse/*' => Http::response([
            'job' => ['id' => 'pjb-123', 'status' => 'COMPLETED'],
            'markdown_full' => 'New parsing results',
        ]),
    ]);

    $link = CustomerAdvisorLink::factory()->parsingInProgress()->create([
        'parsing_results' => 'Existing parsing results',
    ]);

    (new FetchAiLinkParsingResults($link, $link->parsing_job_id))->handle(app(LlamaParseV2Client::class));

    expect($link->refresh()->parsing_results)->toBe('New parsing results');
});

it('keeps waiting on a parse job that is still in progress', function (string $status): void {
    Http::fake([
        'https://api.cloud.llamaindex.ai/api/v2/parse/*' => Http::response([
            'job' => ['id' => 'pjb-123', 'status' => $status],
        ]),
    ]);

    $link = CustomerAdvisorLink::factory()->parsingInProgress()->create([
        'parsing_results' => 'Existing parsing results',
        'parsing_started_at' => now()->subHour(),
        'parsing_last_checked_at' => now()->subHour(),
        'updated_at' => now()->subDay(),
    ]);

    $parsingJobId = $link->parsing_job_id;
    $updatedAt = $link->updated_at;

    (new FetchAiLinkParsingResults($link, $parsingJobId))->handle(app(LlamaParseV2Client::class));

    $link->refresh();

    expect($link->parsing_job_id)->toBe($parsingJobId)
        ->and($link->parsing_results)->toBe('Existing parsing results')
        ->and($link->parsing_failed_at)->toBeNull()
        ->and($link->parsing_last_checked_at->isAfter(now()->subMinute()))->toBeTrue()
        ->and($link->updated_at->toDateTimeString())->toBe($updatedAt->toDateTimeString());
})->with(['PENDING', 'RUNNING']);

it('marks the link as failed and keeps its existing parsing results when the parse job fails', function (string $status): void {
    Exceptions::fake();

    Http::fake([
        'https://api.cloud.llamaindex.ai/api/v2/parse/*' => Http::response([
            'job' => ['id' => 'pjb-123', 'status' => $status, 'error_message' => 'The page could not be fetched.'],
        ]),
    ]);

    $link = CustomerAdvisorLink::factory()->parsingInProgress()->create([
        'parsing_results' => 'Existing parsing results',
        'updated_at' => now()->subDay(),
    ]);

    $updatedAt = $link->updated_at;

    (new FetchAiLinkParsingResults($link, $link->parsing_job_id))->handle(app(LlamaParseV2Client::class));

    $link->refresh();

    expect($link->parsing_failed_at)->not->toBeNull()
        ->and($link->parsing_job_id)->toBeNull()
        ->and($link->parsing_results)->toBe('Existing parsing results')
        ->and($link->updated_at->toDateTimeString())->toBe($updatedAt->toDateTimeString());

    Exceptions::assertReported(AiLinkParsingFailedException::class);
})->with(['FAILED', 'CANCELLED']);

it('marks the link as failed when a completed parse job has no content', function (): void {
    Exceptions::fake();

    Http::fake([
        'https://api.cloud.llamaindex.ai/api/v2/parse/*' => Http::response([
            'job' => ['id' => 'pjb-123', 'status' => 'COMPLETED'],
            'markdown_full' => '',
        ]),
    ]);

    $link = CustomerAdvisorLink::factory()->parsingInProgress()->create([
        'parsing_results' => null,
    ]);

    (new FetchAiLinkParsingResults($link, $link->parsing_job_id))->handle(app(LlamaParseV2Client::class));

    $link->refresh();

    expect($link->parsing_failed_at)->not->toBeNull()
        ->and($link->parsing_results)->toBeNull();

    Exceptions::assertReported(AiLinkParsingFailedException::class);
});

it('gives up on a parse job that has not completed within 2 hours', function (): void {
    Exceptions::fake();

    Http::fake([
        'https://api.cloud.llamaindex.ai/api/v2/parse/*' => Http::response([
            'job' => ['id' => 'pjb-123', 'status' => 'RUNNING'],
        ]),
    ]);

    $link = CustomerAdvisorLink::factory()->parsingInProgress()->create([
        'parsing_results' => null,
        'parsing_started_at' => now()->subHours(2)->subMinute(),
    ]);

    (new FetchAiLinkParsingResults($link, $link->parsing_job_id))->handle(app(LlamaParseV2Client::class));

    $link->refresh();

    expect($link->parsing_failed_at)->not->toBeNull()
        ->and($link->parsing_job_id)->toBeNull();

    Exceptions::assertReported(AiLinkParsingFailedException::class);
});

it('reports an error response and keeps waiting within 2 hours', function (): void {
    Exceptions::fake();

    Http::fake([
        'https://api.cloud.llamaindex.ai/api/v2/parse/*' => Http::response(['detail' => 'Internal server error'], 500),
    ]);

    $link = CustomerAdvisorLink::factory()->parsingInProgress()->create([
        'parsing_results' => null,
    ]);

    $parsingJobId = $link->parsing_job_id;

    (new FetchAiLinkParsingResults($link, $parsingJobId))->handle(app(LlamaParseV2Client::class));

    $link->refresh();

    expect($link->parsing_job_id)->toBe($parsingJobId)
        ->and($link->parsing_failed_at)->toBeNull();

    Exceptions::assertReported(RequestException::class);
    Exceptions::assertNotReported(AiLinkParsingFailedException::class);
});

it('gives up after 2 hours of error responses', function (): void {
    Exceptions::fake();

    Http::fake([
        'https://api.cloud.llamaindex.ai/api/v2/parse/*' => Http::response(['detail' => 'Internal server error'], 500),
    ]);

    $link = CustomerAdvisorLink::factory()->parsingInProgress()->create([
        'parsing_results' => null,
        'parsing_started_at' => now()->subHours(3),
    ]);

    (new FetchAiLinkParsingResults($link, $link->parsing_job_id))->handle(app(LlamaParseV2Client::class));

    $link->refresh();

    expect($link->parsing_failed_at)->not->toBeNull()
        ->and($link->parsing_job_id)->toBeNull();

    Exceptions::assertReported(AiLinkParsingFailedException::class);
});

it('ignores a parse job that is no longer the current one for the link', function (): void {
    Http::fake();

    $link = CustomerAdvisorLink::factory()->parsingInProgress()->create([
        'parsing_results' => null,
    ]);

    $parsingJobId = $link->parsing_job_id;

    (new FetchAiLinkParsingResults($link, 'pjb-older'))->handle(app(LlamaParseV2Client::class));

    Http::assertNothingSent();

    expect($link->refresh()->parsing_job_id)->toBe($parsingJobId);
});
