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
use AdvisingApp\Ai\Jobs\Advisors\FetchAiMessageFileParsingResults;
use AdvisingApp\Ai\Models\AiMessageFile;
use AdvisingApp\Ai\Settings\AiIntegrationsSettings;
use Illuminate\Support\Facades\Http;

use function Pest\Laravel\assertModelExists;
use function Pest\Laravel\assertNotSoftDeleted;

beforeEach(function (): void {
    Http::preventStrayRequests();

    $settings = app(AiIntegrationsSettings::class);
    $settings->llamaparse_api_key = 'test-api-key';
    $settings->save();
});

it('stores the parsing results of a completed parse job', function (): void {
    Http::fake([
        'https://api.cloud.llamaindex.ai/api/v2/parse/*' => Http::response([
            'job' => ['id' => 'pjb-123', 'status' => 'COMPLETED'],
            'markdown_full' => 'Course syllabus',
        ]),
    ]);

    $file = AiMessageFile::factory()->parsingInProgress()->create();

    (new FetchAiMessageFileParsingResults($file))->handle(app(FetchFileParsingResults::class));

    expect($file->refresh()->parsing_results)->toBe('Course syllabus');
});

it('leaves the file for the chat to handle when its parse job has not completed', function (array $parseJob): void {
    Http::fake([
        'https://api.cloud.llamaindex.ai/api/v2/parse/*' => Http::response($parseJob),
    ]);

    $file = AiMessageFile::factory()->parsingInProgress()->create();

    (new FetchAiMessageFileParsingResults($file))->handle(app(FetchFileParsingResults::class));

    expect($file->refresh()->parsing_results)->toBeNull();

    assertNotSoftDeleted($file);
})->with([
    'in progress' => [['job' => ['id' => 'pjb-123', 'status' => 'RUNNING']]],
    'failed' => [['job' => ['id' => 'pjb-123', 'status' => 'FAILED', 'error_message' => 'The file could not be read.']]],
]);

it('does not check on the parse job of a file that was deleted or parsed after this was queued', function (Closure $changeFile): void {
    Http::fake();

    $file = AiMessageFile::factory()->parsingInProgress()->create();

    $job = new FetchAiMessageFileParsingResults($file);

    $changeFile($file);

    $job->handle(app(FetchFileParsingResults::class));

    Http::assertNothingSent();

    assertModelExists($file);
})->with([
    'deleted' => [fn (AiMessageFile $file): bool => AiMessageFile::query()->whereKey($file->getKey())->delete() > 0],
    'parsed' => [fn (AiMessageFile $file): bool => AiMessageFile::query()->whereKey($file->getKey())->update(['parsing_results' => 'Course syllabus']) > 0],
]);
