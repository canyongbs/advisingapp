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

use AdvisingApp\Ai\Actions\GetLlamaParseWebhookSigningSecret;
use AdvisingApp\Ai\Jobs\Advisors\FetchAiAssistantFileParsingResults;
use AdvisingApp\Ai\Jobs\Advisors\FetchAiMessageFileParsingResults;
use AdvisingApp\Ai\Jobs\CustomerAdvisors\FetchCustomerAdvisorFileParsingResults;
use AdvisingApp\Ai\Jobs\FetchAiLinkParsingResults;
use AdvisingApp\Ai\Models\AiAssistant;
use AdvisingApp\Ai\Models\AiAssistantFile;
use AdvisingApp\Ai\Models\AiAssistantLink;
use AdvisingApp\Ai\Models\AiMessageFile;
use AdvisingApp\Ai\Models\Contracts\AiFile;
use AdvisingApp\Ai\Models\CustomerAdvisorFile;
use AdvisingApp\Ai\Models\CustomerAdvisorLink;
use App\Features\LlamaParseFileParsingFeature;
use App\Features\LlamaParseLinkParsingFeature;
use App\Models\Tenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Queue;
use Illuminate\Testing\TestResponse;

use function Pest\Laravel\call;

$sendSignedWebhook = function (array $payload): TestResponse {
    $tenant = Tenant::current();

    assert($tenant instanceof Tenant);

    $content = json_encode($payload, JSON_THROW_ON_ERROR);

    return call(
        'POST',
        route('inbound.webhook.llamaparse'),
        server: [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_X_WEBHOOK_EVENT_TYPE' => 'parse.success',
            'HTTP_LC_SIGNATURE' => 'sha256=' . hash_hmac('sha256', $content, app(GetLlamaParseWebhookSigningSecret::class)($tenant)),
        ],
        content: $content,
    );
};

it('dispatches a check of the parse job for the link it belongs to', function (string $linkModel) use ($sendSignedWebhook): void {
    $link = $linkModel::factory()->parsingInProgress()->create();

    Queue::fake();

    $sendSignedWebhook([
        'event_id' => 'evt-123',
        'event_type' => 'parse.success',
        'data' => ['id' => $link->parsing_job_id, 'job_id' => $link->parsing_job_id],
    ])->assertNoContent();

    Queue::assertPushed(FetchAiLinkParsingResults::class, fn (FetchAiLinkParsingResults $job): bool => $job->uniqueId() === $link->getKey());
})->with([
    'customer advisor link' => [CustomerAdvisorLink::class],
    'AI assistant link' => [AiAssistantLink::class],
]);

$fileDataset = [
    'customer advisor file' => [
        fn (): CustomerAdvisorFile => CustomerAdvisorFile::factory()->parsingInProgress()->create(),
        FetchCustomerAdvisorFileParsingResults::class,
    ],
    'AI assistant file' => [
        fn (): AiAssistantFile => AiAssistantFile::factory()->parsingInProgress()->create(['assistant_id' => AiAssistant::factory()]),
        FetchAiAssistantFileParsingResults::class,
    ],
    'chat file' => [
        fn (): AiMessageFile => AiMessageFile::factory()->parsingInProgress()->create(),
        FetchAiMessageFileParsingResults::class,
    ],
];

it('dispatches a check of the parse job for the file it belongs to', function (Closure $createFile, string $jobClass) use ($sendSignedWebhook): void {
    $file = $createFile();

    assert($file instanceof Model && $file instanceof AiFile);

    Queue::fake();

    $sendSignedWebhook([
        'event_id' => 'evt-123',
        'event_type' => 'parse.success',
        'data' => ['id' => $file->getFileId(), 'job_id' => $file->getFileId()],
    ])->assertNoContent();

    Queue::assertPushed($jobClass, fn (FetchCustomerAdvisorFileParsingResults|FetchAiAssistantFileParsingResults|FetchAiMessageFileParsingResults $job): bool => $job->uniqueId() === $file->getKey());
    Queue::assertCount(1);
})->with($fileDataset);

// TODO: Cleanup Task (llamaparse-link-parsing): remove this test along with the feature flag.
it('dispatches a check of the parse job for a file while LlamaParse link parsing is inactive', function () use ($sendSignedWebhook): void {
    $file = CustomerAdvisorFile::factory()->parsingInProgress()->create();

    LlamaParseLinkParsingFeature::deactivate();

    Queue::fake();

    $sendSignedWebhook([
        'event_id' => 'evt-123',
        'event_type' => 'parse.success',
        'data' => ['id' => $file->file_id, 'job_id' => $file->file_id],
    ])->assertNoContent();

    Queue::assertPushed(FetchCustomerAdvisorFileParsingResults::class, fn (FetchCustomerAdvisorFileParsingResults $job): bool => $job->uniqueId() === $file->getKey());
});

// TODO: Cleanup Task (llamaparse-file-parsing): remove this test along with the feature flag.
it('ignores a webhook for a file while LlamaParse file parsing is inactive', function (Closure $createFile) use ($sendSignedWebhook): void {
    $file = $createFile();

    assert($file instanceof Model && $file instanceof AiFile);

    LlamaParseFileParsingFeature::deactivate();

    Queue::fake();

    $sendSignedWebhook([
        'event_id' => 'evt-123',
        'event_type' => 'parse.success',
        'data' => ['id' => $file->getFileId(), 'job_id' => $file->getFileId()],
    ])->assertNoContent();

    Queue::assertNothingPushed();
})->with($fileDataset);

it('ignores a webhook for a file that was deleted', function (Closure $createFile) use ($sendSignedWebhook): void {
    $file = $createFile();

    assert($file instanceof Model && $file instanceof AiFile);

    $file->delete();

    Queue::fake();

    $sendSignedWebhook([
        'event_id' => 'evt-123',
        'event_type' => 'parse.success',
        'data' => ['id' => $file->getFileId(), 'job_id' => $file->getFileId()],
    ])->assertNoContent();

    Queue::assertNothingPushed();
})->with($fileDataset);

it('ignores a webhook for a parse job that no link or file is waiting on', function (array $data) use ($sendSignedWebhook): void {
    CustomerAdvisorLink::factory()->parsingInProgress()->create();
    CustomerAdvisorFile::factory()->parsingInProgress()->create();

    Queue::fake();

    $sendSignedWebhook([
        'event_id' => 'evt-123',
        'event_type' => 'parse.success',
        'data' => $data,
    ])->assertNoContent();

    Queue::assertNothingPushed();
})->with([
    'unknown job ID' => [['id' => 'pjb-unknown', 'job_id' => 'pjb-unknown']],
    'no job ID' => [[]],
]);

// TODO: Cleanup Task (llamaparse-link-parsing): remove this test along with the feature flag.
it('ignores a webhook while LlamaParse link parsing is inactive', function () use ($sendSignedWebhook): void {
    $link = CustomerAdvisorLink::factory()->parsingInProgress()->create();

    LlamaParseLinkParsingFeature::deactivate();

    Queue::fake();

    $sendSignedWebhook([
        'event_id' => 'evt-123',
        'event_type' => 'parse.success',
        'data' => ['id' => $link->parsing_job_id, 'job_id' => $link->parsing_job_id],
    ])->assertNoContent();

    Queue::assertNothingPushed();
});
