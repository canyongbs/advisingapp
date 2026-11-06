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

use AdvisingApp\Ai\Actions\FetchFileParsingResults;
use AdvisingApp\Ai\Exceptions\FileParsingFailedException;
use AdvisingApp\Ai\Exceptions\FileParsingTooManyPagesException;
use AdvisingApp\Ai\Settings\AiIntegrationsSettings;
use App\Features\LlamaParseFileParsingFeature;
use Illuminate\Http\Client\Request;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Exceptions;
use Illuminate\Support\Facades\Http;

beforeEach(function (): void {
    Http::preventStrayRequests();

    $settings = app(AiIntegrationsSettings::class);
    $settings->llamaparse_api_key = 'test-api-key';
    $settings->save();
});

it('returns the parsing results of a completed parse job', function (string $mimeType, string $expand, array $content, string $parsingResults): void {
    Http::fake([
        'https://api.cloud.llamaindex.ai/api/v2/parse/*' => Http::response([
            'job' => ['id' => 'pjb-123', 'status' => 'COMPLETED'],
            ...$content,
        ]),
    ]);

    $result = app(FetchFileParsingResults::class)->execute('pjb-123', $mimeType);

    expect($result->fileId)->toBe('pjb-123')
        ->and($result->parsingResults)->toBe($parsingResults);

    Http::assertSent(fn (Request $request): bool => $request->url() === "https://api.cloud.llamaindex.ai/api/v2/parse/pjb-123?expand={$expand}");
})->with([
    'document' => ['application/pdf', 'markdown_full', ['markdown_full' => '# Course syllabus'], '# Course syllabus'],
    'audio with a plain-text transcript' => ['audio/mpeg', 'text_full,markdown_full', ['text_full' => 'Course syllabus', 'markdown_full' => '# Course syllabus'], 'Course syllabus'],
    'audio with only a markdown transcript' => ['audio/mpeg', 'text_full,markdown_full', ['text_full' => '', 'markdown_full' => '# Course syllabus'], '# Course syllabus'],
    'video with a plain-text transcript' => ['video/mp4', 'text_full,markdown_full', ['text_full' => 'Course syllabus'], 'Course syllabus'],
]);

it('returns no parsing results while the parse job is still in progress', function (string $status): void {
    Http::fake([
        'https://api.cloud.llamaindex.ai/api/v2/parse/*' => Http::response([
            'job' => ['id' => 'pjb-123', 'status' => $status],
        ]),
    ]);

    $result = app(FetchFileParsingResults::class)->execute('pjb-123', 'application/pdf');

    expect($result->fileId)->toBe('pjb-123')
        ->and($result->parsingResults)->toBeNull();
})->with(['PENDING', 'RUNNING']);

it('throws a generic parsing failure when the parse job fails', function (string $status): void {
    Http::fake([
        'https://api.cloud.llamaindex.ai/api/v2/parse/*' => Http::response([
            'job' => ['id' => 'pjb-123', 'status' => $status, 'error_message' => 'The file could not be read.'],
        ]),
    ]);

    expect(fn () => app(FetchFileParsingResults::class)->execute('pjb-123', 'application/pdf'))
        ->toThrow(FileParsingFailedException::class, "LlamaParse reported the parse job [pjb-123] as {$status}: The file could not be read.");
})->with(['FAILED', 'CANCELLED']);

it('throws a generic parsing failure when a completed parse job has no content', function (): void {
    Http::fake([
        'https://api.cloud.llamaindex.ai/api/v2/parse/*' => Http::response([
            'job' => ['id' => 'pjb-123', 'status' => 'COMPLETED'],
            'markdown_full' => '',
        ]),
    ]);

    expect(fn () => app(FetchFileParsingResults::class)->execute('pjb-123', 'application/pdf'))
        ->toThrow(FileParsingFailedException::class, 'LlamaParse completed the parse job [pjb-123] without any content.');
});

it('fetches the parsing results from V1 for a file that was submitted to V1', function (): void {
    $fileId = '3b0b1f7e-2c6a-4f43-9d5f-1d2a2b7f9c10';

    Http::fake([
        'https://api.cloud.llamaindex.ai/api/v2/parse/*' => Http::response(['detail' => 'Parse job not found'], 404),
        "https://api.cloud.llamaindex.ai/api/v1/parsing/job/{$fileId}/result/markdown" => Http::response(['markdown' => 'Course syllabus']),
        "https://api.cloud.llamaindex.ai/api/v1/parsing/job/{$fileId}" => Http::response(['id' => $fileId, 'status' => 'SUCCESS']),
    ]);

    $result = app(FetchFileParsingResults::class)->execute($fileId, 'application/pdf');

    expect($result->parsingResults)->toBe('Course syllabus');
});

it('reports an error response and returns no parsing results', function (): void {
    Exceptions::fake();

    Http::fake([
        'https://api.cloud.llamaindex.ai/api/v2/parse/*' => Http::response(['detail' => 'Internal server error'], 500),
    ]);

    $result = app(FetchFileParsingResults::class)->execute('pjb-123', 'application/pdf');

    expect($result->parsingResults)->toBeNull();

    Exceptions::assertReported(RequestException::class);

    Http::assertSentCount(1);
});

// TODO: Cleanup Task (llamaparse-file-parsing): remove this test along with the feature flag.
it('fetches the parsing results from V1 while LlamaParse file parsing is inactive', function (): void {
    LlamaParseFileParsingFeature::deactivate();

    Http::fake([
        'https://api.cloud.llamaindex.ai/api/v1/parsing/job/pjb-123/result/markdown' => Http::response(['markdown' => 'Course syllabus']),
        'https://api.cloud.llamaindex.ai/api/v1/parsing/job/pjb-123' => Http::response(['id' => 'pjb-123', 'status' => 'SUCCESS']),
    ]);

    $result = app(FetchFileParsingResults::class)->execute('pjb-123', 'application/pdf');

    expect($result->parsingResults)->toBe('Course syllabus');

    Http::assertNotSent(fn (Request $request): bool => str_contains($request->url(), '/api/v2/'));
});

// TODO: Cleanup Task (llamaparse-file-parsing): remove this test along with the feature flag.
it('throws a too many pages failure from V1 while LlamaParse file parsing is inactive', function (): void {
    LlamaParseFileParsingFeature::deactivate();

    Http::fake([
        'https://api.cloud.llamaindex.ai/api/v1/parsing/job/pjb-123' => Http::response(['id' => 'pjb-123', 'status' => 'ERROR', 'error_code' => 'TOO_MANY_PAGES']),
    ]);

    expect(fn () => app(FetchFileParsingResults::class)->execute('pjb-123', 'application/pdf'))
        ->toThrow(FileParsingTooManyPagesException::class);
});
