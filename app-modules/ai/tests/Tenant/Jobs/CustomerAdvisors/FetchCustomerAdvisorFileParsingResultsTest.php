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
use AdvisingApp\Ai\Enums\FileParsingError;
use AdvisingApp\Ai\Jobs\CustomerAdvisors\FetchCustomerAdvisorFileParsingResults;
use AdvisingApp\Ai\Models\CustomerAdvisorFile;
use AdvisingApp\Ai\Notifications\FileParsingFailedNotification;
use AdvisingApp\Ai\Settings\AiIntegrationsSettings;
use App\Features\LlamaParseFileParsingFeature;
use App\Models\User;
use Illuminate\Pipeline\Pipeline;
use Illuminate\Queue\Middleware\WithoutOverlapping;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Notification;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertModelExists;
use function Pest\Laravel\assertNotSoftDeleted;
use function Pest\Laravel\assertSoftDeleted;

$createFileBeingParsed = fn (array $attributes = []): CustomerAdvisorFile => CustomerAdvisorFile::factory()->parsingInProgress()->create($attributes);

beforeEach(function (): void {
    Notification::fake();
    Http::preventStrayRequests();

    $settings = app(AiIntegrationsSettings::class);
    $settings->llamaparse_api_key = 'test-api-key';
    $settings->save();
});

it('stores the parsing results of a completed parse job', function () use ($createFileBeingParsed): void {
    Http::fake([
        'https://api.cloud.llamaindex.ai/api/v2/parse/*' => Http::response([
            'job' => ['id' => 'pjb-123', 'status' => 'COMPLETED'],
            'markdown_full' => 'Course syllabus',
        ]),
    ]);

    $file = $createFileBeingParsed(['updated_at' => now()->subDay()]);

    $updatedAt = $file->updated_at;

    (new FetchCustomerAdvisorFileParsingResults($file))->handle(app(FetchFileParsingResults::class));

    $file->refresh();

    // The vector store uploads a file again once its `updated_at` changes.
    expect($file->parsing_results)->toBe('Course syllabus')
        ->and($file->updated_at->isAfter($updatedAt))->toBeTrue();
});

it('records the check without changing the `updated_at` of the file while its parse job is in progress', function () use ($createFileBeingParsed): void {
    Http::fake([
        'https://api.cloud.llamaindex.ai/api/v2/parse/*' => Http::response([
            'job' => ['id' => 'pjb-123', 'status' => 'RUNNING'],
        ]),
    ]);

    $file = $createFileBeingParsed([
        'parsing_last_checked_at' => now()->subHour(),
        'updated_at' => now()->subDay(),
    ]);

    $updatedAt = $file->updated_at;

    (new FetchCustomerAdvisorFileParsingResults($file))->handle(app(FetchFileParsingResults::class));

    $file->refresh();

    expect($file->parsing_results)->toBeNull()
        ->and($file->parsing_last_checked_at->isAfter(now()->subMinute()))->toBeTrue()
        ->and($file->updated_at->toDateTimeString())->toBe($updatedAt->toDateTimeString());

    assertNotSoftDeleted($file);
});

it('notifies the uploader and deletes the file when its parse job fails', function () use ($createFileBeingParsed): void {
    Http::fake([
        'https://api.cloud.llamaindex.ai/api/v2/parse/*' => Http::response([
            'job' => ['id' => 'pjb-123', 'status' => 'FAILED', 'error_message' => 'The file could not be read.'],
        ]),
    ]);

    $user = User::factory()->create();

    actingAs($user);

    $file = $createFileBeingParsed();

    (new FetchCustomerAdvisorFileParsingResults($file))->handle(app(FetchFileParsingResults::class));

    assertSoftDeleted($file);

    Notification::assertSentTo($user, FileParsingFailedNotification::class, fn (FileParsingFailedNotification $notification): bool => $notification->error === FileParsingError::Generic);
});

it('gives up on a parse job that has not completed within 2 hours', function () use ($createFileBeingParsed): void {
    Http::fake([
        'https://api.cloud.llamaindex.ai/api/v2/parse/*' => Http::response([
            'job' => ['id' => 'pjb-123', 'status' => 'RUNNING'],
        ]),
    ]);

    $user = User::factory()->create();

    actingAs($user);

    $file = $createFileBeingParsed(['parsing_started_at' => now()->subHours(2)->subMinute()]);

    assertNotSoftDeleted($file);

    (new FetchCustomerAdvisorFileParsingResults($file))->handle(app(FetchFileParsingResults::class));

    assertSoftDeleted($file);

    Notification::assertSentTo($user, FileParsingFailedNotification::class, fn (FileParsingFailedNotification $notification): bool => $notification->error === FileParsingError::Generic);
});

// TODO: Cleanup Task (llamaparse-file-parsing): remove this test once no file is still being parsed on V1.
it('times a file that was submitted to V1 from when it was uploaded', function (int $uploadedMinutesAgo, bool $isGivenUpOn) use ($createFileBeingParsed): void {
    $fileId = '3b0b1f7e-2c6a-4f43-9d5f-1d2a2b7f9c10';

    Http::fake([
        'https://api.cloud.llamaindex.ai/api/v2/parse/*' => Http::response(['detail' => 'Parse job not found'], 404),
        "https://api.cloud.llamaindex.ai/api/v1/parsing/job/{$fileId}" => Http::response(['id' => $fileId, 'status' => 'PENDING']),
    ]);

    $file = $createFileBeingParsed([
        'file_id' => $fileId,
        'parsing_started_at' => null,
        'parsing_last_checked_at' => null,
        'created_at' => now()->subMinutes($uploadedMinutesAgo),
    ]);

    $createdAt = $file->created_at;

    (new FetchCustomerAdvisorFileParsingResults($file))->handle(app(FetchFileParsingResults::class));

    $file->refresh();

    expect($file->parsing_started_at->toDateTimeString())->toBe($createdAt->toDateTimeString())
        ->and($file->trashed())->toBe($isGivenUpOn);
})->with([
    'uploaded within 2 hours' => [30, false],
    'uploaded over 2 hours ago' => [121, true],
]);

it('does not check on the parse job of a file that was deleted or parsed after this was queued', function (Closure $changeFile) use ($createFileBeingParsed): void {
    Http::fake();

    $file = $createFileBeingParsed();

    $job = new FetchCustomerAdvisorFileParsingResults($file);

    $changeFile($file);

    $job->handle(app(FetchFileParsingResults::class));

    Http::assertNothingSent();

    Notification::assertNothingSent();

    assertModelExists($file);
})->with([
    'deleted' => [fn (CustomerAdvisorFile $file): bool => CustomerAdvisorFile::query()->whereKey($file->getKey())->delete() > 0],
    'parsed' => [fn (CustomerAdvisorFile $file): bool => CustomerAdvisorFile::query()->whereKey($file->getKey())->update(['parsing_results' => 'Course syllabus']) > 0],
]);

it('does not check on the parse job while another check of the same file is running', function () use ($createFileBeingParsed): void {
    Http::fake();

    $job = new FetchCustomerAdvisorFileParsingResults($createFileBeingParsed());

    $middleware = $job->middleware();

    assert($middleware[0] instanceof WithoutOverlapping);

    $runningCheckLock = Cache::lock($middleware[0]->getLockKey($job), 120);

    expect($runningCheckLock->get())->toBeTrue();

    (new Pipeline(app()))
        ->send($job)
        ->through($middleware)
        ->then(fn (FetchCustomerAdvisorFileParsingResults $job) => $job->handle(app(FetchFileParsingResults::class)));

    $runningCheckLock->release();

    Http::assertNothingSent();
});

// TODO: Cleanup Task (llamaparse-file-parsing): remove this test along with the feature flag.
it('does not record the check or give up on the file while LlamaParse file parsing is inactive', function () use ($createFileBeingParsed): void {
    LlamaParseFileParsingFeature::deactivate();

    Http::fake([
        'https://api.cloud.llamaindex.ai/api/v1/parsing/job/*' => Http::response(['status' => 'PENDING']),
    ]);

    $file = $createFileBeingParsed([
        'parsing_started_at' => null,
        'parsing_last_checked_at' => null,
        'created_at' => now()->subHours(3),
    ]);

    (new FetchCustomerAdvisorFileParsingResults($file))->handle(app(FetchFileParsingResults::class));

    $file->refresh();

    expect($file->parsing_started_at)->toBeNull()
        ->and($file->parsing_last_checked_at)->toBeNull()
        ->and($file->trashed())->toBeFalse();
});
