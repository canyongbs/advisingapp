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

use AdvisingApp\Ai\DataTransferObjects\LlamaParseV2ParseJobData;
use AdvisingApp\Ai\Enums\LlamaParseV2JobStatus;
use AdvisingApp\Ai\Enums\LlamaParseV2Tier;
use AdvisingApp\Ai\Exceptions\LlamaParseApiKeyNotConfiguredException;
use AdvisingApp\Ai\Services\LlamaParse\LlamaParseV2Client;
use AdvisingApp\Ai\Settings\AiIntegrationsSettings;
use Illuminate\Http\Client\Request;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;

beforeEach(function (): void {
    Http::preventStrayRequests();

    $settings = app(AiIntegrationsSettings::class);
    $settings->llamaparse_api_key = 'test-api-key';
    $settings->save();
});

describe('creating a parse job', function (): void {
    it('submits the parse job pinned to the version and returns its ID', function (): void {
        Http::fake([
            'https://api.cloud.llamaindex.ai/api/v2/parse' => Http::response(['id' => 'pjb-123', 'status' => 'PENDING']),
        ]);

        $jobId = app(LlamaParseV2Client::class)->createParseJob(LlamaParseV2Tier::CostEffective, [
            'source_url' => 'https://example.com',
        ]);

        expect($jobId)->toBe('pjb-123');

        Http::assertSent(fn (Request $request): bool => $request->method() === 'POST'
            && $request->url() === 'https://api.cloud.llamaindex.ai/api/v2/parse'
            && $request->hasHeader('Authorization', 'Bearer test-api-key')
            && $request->data() === [
                'source_url' => 'https://example.com',
                'tier' => 'cost_effective',
                'version' => LlamaParseV2Client::VERSION,
            ]);
    });

    it('does not let the parameters override the tier or the pinned version', function (): void {
        Http::fake([
            'https://api.cloud.llamaindex.ai/api/v2/parse' => Http::response(['id' => 'pjb-123']),
        ]);

        app(LlamaParseV2Client::class)->createParseJob(LlamaParseV2Tier::CostEffective, [
            'source_url' => 'https://example.com',
            'tier' => 'agentic',
            'version' => 'latest',
        ]);

        Http::assertSent(fn (Request $request): bool => ($request['tier'] === 'cost_effective')
            && ($request['version'] === LlamaParseV2Client::VERSION));
    });

    it('throws when LlamaParse responds with an error', function (): void {
        Http::fake([
            'https://api.cloud.llamaindex.ai/api/v2/parse' => Http::response(['detail' => 'Invalid API key'], 401),
        ]);

        expect(fn (): string => app(LlamaParseV2Client::class)->createParseJob(LlamaParseV2Tier::CostEffective, [
            'source_url' => 'https://example.com',
        ]))->toThrow(RequestException::class);
    });

    it('throws when LlamaParse does not return a job ID', function (): void {
        Http::fake([
            'https://api.cloud.llamaindex.ai/api/v2/parse' => Http::response([]),
        ]);

        expect(fn (): string => app(LlamaParseV2Client::class)->createParseJob(LlamaParseV2Tier::CostEffective, [
            'source_url' => 'https://example.com',
        ]))->toThrow(UnexpectedValueException::class);
    });
});

describe('getting a parse job', function (): void {
    it('returns the status and the requested markdown of the parse job', function (): void {
        Http::fake([
            'https://api.cloud.llamaindex.ai/api/v2/parse/pjb-123*' => Http::response([
                'job' => [
                    'id' => 'pjb-123',
                    'status' => 'COMPLETED',
                ],
                'markdown_full' => '# Course Schedule',
            ]),
        ]);

        $job = app(LlamaParseV2Client::class)->getParseJob('pjb-123', ['markdown_full']);

        expect($job->id)->toBe('pjb-123')
            ->and($job->status)->toBe(LlamaParseV2JobStatus::Completed)
            ->and($job->markdownFull)->toBe('# Course Schedule')
            ->and($job->errorMessage)->toBeNull();

        Http::assertSent(fn (Request $request): bool => $request->method() === 'GET'
            && $request->url() === 'https://api.cloud.llamaindex.ai/api/v2/parse/pjb-123?expand=markdown_full'
            && $request->hasHeader('Authorization', 'Bearer test-api-key'));
    });

    it('returns the error message of a failed parse job', function (): void {
        Http::fake([
            'https://api.cloud.llamaindex.ai/api/v2/parse/pjb-123*' => Http::response([
                'job' => [
                    'id' => 'pjb-123',
                    'status' => 'FAILED',
                    'error_message' => 'The page could not be fetched.',
                ],
            ]),
        ]);

        $job = app(LlamaParseV2Client::class)->getParseJob('pjb-123', ['markdown_full']);

        expect($job->status)->toBe(LlamaParseV2JobStatus::Failed)
            ->and($job->errorMessage)->toBe('The page could not be fetched.')
            ->and($job->markdownFull)->toBeNull();
    });

    it('does not request any content alongside the job by default', function (): void {
        Http::fake([
            'https://api.cloud.llamaindex.ai/api/v2/parse/pjb-123*' => Http::response([
                'job' => [
                    'id' => 'pjb-123',
                    'status' => 'RUNNING',
                ],
            ]),
        ]);

        app(LlamaParseV2Client::class)->getParseJob('pjb-123');

        Http::assertSent(fn (Request $request): bool => $request->url() === 'https://api.cloud.llamaindex.ai/api/v2/parse/pjb-123');
    });

    it('encodes the job ID in the request path', function (): void {
        Http::fake([
            'https://api.cloud.llamaindex.ai/api/v2/parse/*' => Http::response([
                'job' => [
                    'id' => 'pjb-123/../versions',
                    'status' => 'RUNNING',
                ],
            ]),
        ]);

        app(LlamaParseV2Client::class)->getParseJob('pjb-123/../versions');

        Http::assertSent(fn (Request $request): bool => $request->url() === 'https://api.cloud.llamaindex.ai/api/v2/parse/pjb-123%2F..%2Fversions');
    });

    it('throws when LlamaParse responds with an error', function (): void {
        Http::fake([
            'https://api.cloud.llamaindex.ai/api/v2/parse/pjb-123*' => Http::response(['detail' => 'Not found'], 404),
        ]);

        expect(fn () => app(LlamaParseV2Client::class)->getParseJob('pjb-123'))
            ->toThrow(RequestException::class);
    });

    it('throws when LlamaParse returns an unknown status', function (): void {
        Http::fake([
            'https://api.cloud.llamaindex.ai/api/v2/parse/pjb-123*' => Http::response([
                'job' => [
                    'id' => 'pjb-123',
                    'status' => 'SOMETHING_NEW',
                ],
            ]),
        ]);

        expect(fn () => app(LlamaParseV2Client::class)->getParseJob('pjb-123'))
            ->toThrow(UnexpectedValueException::class);
    });
});

it('does not send a request when the API key is not configured', function (?string $apiKey, Closure $callClient): void {
    $settings = app(AiIntegrationsSettings::class);
    $settings->llamaparse_api_key = $apiKey;
    $settings->save();

    Http::fake();

    expect(fn () => $callClient(app(LlamaParseV2Client::class)))
        ->toThrow(LlamaParseApiKeyNotConfiguredException::class);

    Http::assertNothingSent();
})->with([
    'missing' => [null],
    'empty' => [''],
])->with([
    'creating a parse job' => [fn (LlamaParseV2Client $client): string => $client->createParseJob(LlamaParseV2Tier::CostEffective, [
        'source_url' => 'https://example.com',
    ])],
    'getting a parse job' => [fn (LlamaParseV2Client $client): LlamaParseV2ParseJobData => $client->getParseJob('pjb-123')],
]);
