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
use AdvisingApp\Ai\Jobs\Advisors\FetchAiAssistantFileParsingResults;
use AdvisingApp\Ai\Jobs\AiAssistants\FetchAiAssistantLinkParsingResults;
use AdvisingApp\Ai\Jobs\CustomerAdvisors\FetchCustomerAdvisorFileParsingResults;
use AdvisingApp\Ai\Jobs\CustomerAdvisors\FetchCustomerAdvisorLinkParsingResults;
use AdvisingApp\Ai\Jobs\FetchAiFilesParsingResults;
use AdvisingApp\Ai\Jobs\FetchAiLinkParsingResults;
use AdvisingApp\Ai\Jobs\SubmitAiLinkForParsing;
use AdvisingApp\Ai\Models\AiAssistant;
use AdvisingApp\Ai\Models\AiAssistantFile;
use AdvisingApp\Ai\Models\AiAssistantLink;
use AdvisingApp\Ai\Models\CustomerAdvisorFile;
use AdvisingApp\Ai\Models\CustomerAdvisorLink;
use AdvisingApp\Ai\Services\LlamaParse\LlamaParseV2Client;
use AdvisingApp\Ai\Settings\AiIntegrationsSettings;
use App\Features\LlamaParseLinkParsingFeature;
use Illuminate\Support\Facades\Exceptions;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Queue;

it('dispatches a parsing results job for each recent unparsed file and submits each recent unparsed link', function (): void {
    Queue::fake();

    $assistantFile = AiAssistantFile::factory()->create(['assistant_id' => AiAssistant::factory(), 'parsing_results' => null]);
    $assistantLink = AiAssistantLink::factory()->create(['parsing_results' => null]);
    $advisorFile = CustomerAdvisorFile::factory()->create(['parsing_results' => null]);
    $advisorLink = CustomerAdvisorLink::factory()->create(['parsing_results' => null]);

    // Discard anything pushed by model observers while arranging the records.
    Queue::fake();

    (new FetchAiFilesParsingResults())->handle();

    Queue::assertPushed(FetchAiAssistantFileParsingResults::class, fn (FetchAiAssistantFileParsingResults $job): bool => $job->uniqueId() === $assistantFile->getKey());
    Queue::assertPushed(FetchCustomerAdvisorFileParsingResults::class, fn (FetchCustomerAdvisorFileParsingResults $job): bool => $job->uniqueId() === $advisorFile->getKey());
    Queue::assertPushed(SubmitAiLinkForParsing::class, fn (SubmitAiLinkForParsing $job): bool => $job->uniqueId() === $assistantLink->getKey());
    Queue::assertPushed(SubmitAiLinkForParsing::class, fn (SubmitAiLinkForParsing $job): bool => $job->uniqueId() === $advisorLink->getKey());
    Queue::assertNotPushed(FetchAiAssistantLinkParsingResults::class);
    Queue::assertNotPushed(FetchCustomerAdvisorLinkParsingResults::class);
});

it('does not dispatch for files and links that are already parsed or older than an hour', function (): void {
    Queue::fake();

    AiAssistantFile::factory()->create(['assistant_id' => AiAssistant::factory(), 'parsing_results' => 'Parsed']);
    AiAssistantFile::factory()->create(['assistant_id' => AiAssistant::factory(), 'parsing_results' => null, 'created_at' => now()->subHours(2)]);
    AiAssistantLink::factory()->create(['parsing_results' => 'Parsed']);
    AiAssistantLink::factory()->create(['parsing_results' => null, 'created_at' => now()->subHours(2)]);
    CustomerAdvisorFile::factory()->create(['parsing_results' => 'Parsed']);
    CustomerAdvisorFile::factory()->create(['parsing_results' => null, 'created_at' => now()->subHours(2)]);
    CustomerAdvisorLink::factory()->create(['parsing_results' => 'Parsed']);
    CustomerAdvisorLink::factory()->create(['parsing_results' => null, 'created_at' => now()->subHours(2)]);

    // Discard anything pushed by model observers while arranging the records.
    Queue::fake();

    (new FetchAiFilesParsingResults())->handle();

    Queue::assertNothingPushed();
});

it('submits a link again only once 5 minutes have passed since the last attempt', function (): void {
    Queue::fake();

    $recentlyAttemptedLink = CustomerAdvisorLink::factory()->create([
        'parsing_results' => null,
        'created_at' => now()->subMinutes(20),
        'parsing_last_checked_at' => now()->subMinutes(2),
    ]);

    $dueLink = CustomerAdvisorLink::factory()->create([
        'parsing_results' => null,
        'created_at' => now()->subMinutes(20),
        'parsing_last_checked_at' => now()->subMinutes(6),
    ]);

    (new FetchAiFilesParsingResults())->handle();

    Queue::assertPushed(SubmitAiLinkForParsing::class, fn (SubmitAiLinkForParsing $job): bool => $job->uniqueId() === $dueLink->getKey());
    Queue::assertNotPushed(SubmitAiLinkForParsing::class, fn (SubmitAiLinkForParsing $job): bool => $job->uniqueId() === $recentlyAttemptedLink->getKey());
});

it('does not submit links that have a parse job in progress or have failed', function (Closure $applyState): void {
    Queue::fake();

    $applyState(CustomerAdvisorLink::factory())->create(['parsing_results' => null]);

    (new FetchAiFilesParsingResults())->handle();

    Queue::assertNotPushed(SubmitAiLinkForParsing::class);
})->with([
    'in progress' => [fn (CustomerAdvisorLinkFactory $factory): CustomerAdvisorLinkFactory => $factory->parsingInProgress()],
    'failed' => [fn (CustomerAdvisorLinkFactory $factory): CustomerAdvisorLinkFactory => $factory->parsingFailed()],
]);

it('marks links that could not be submitted within an hour as failed', function (): void {
    Exceptions::fake();
    Queue::fake();

    $link = CustomerAdvisorLink::factory()->create([
        'parsing_results' => null,
        'created_at' => now()->subHour()->subMinute(),
        'parsing_last_checked_at' => now()->subMinutes(3),
    ]);

    expect($link->parsing_failed_at)->toBeNull();

    (new FetchAiFilesParsingResults())->handle();

    expect($link->refresh()->parsing_failed_at)->not->toBeNull();

    Exceptions::assertReported(AiLinkParsingFailedException::class);
    Queue::assertNotPushed(SubmitAiLinkForParsing::class);
});

it('does not mark old links that were never submitted as failed', function (): void {
    Exceptions::fake();
    Queue::fake();

    $link = CustomerAdvisorLink::factory()->create([
        'parsing_results' => null,
        'created_at' => now()->subDays(2),
    ]);

    (new FetchAiFilesParsingResults())->handle();

    expect($link->refresh()->parsing_failed_at)->toBeNull();

    Exceptions::assertNotReported(AiLinkParsingFailedException::class);
});

it('does not mark an old link as failed while its Keep Current refresh is waiting to be retried', function (): void {
    Exceptions::fake();
    Queue::fake();
    Http::preventStrayRequests();

    Http::fake([
        'https://api.cloud.llamaindex.ai/api/v2/parse' => Http::response(['detail' => 'Internal server error'], 500),
    ]);

    $settings = app(AiIntegrationsSettings::class);
    $settings->llamaparse_api_key = 'test-api-key';
    $settings->save();

    $link = CustomerAdvisorLink::factory()->create([
        'parsing_results' => null,
        'created_at' => now()->subMonths(2),
    ]);

    $refresh = (new SubmitAiLinkForParsing($link, refreshExistingParsingResults: true))->withFakeQueueInteractions();

    $refresh->handle(app(LlamaParseV2Client::class));

    $refresh->assertReleased();

    (new FetchAiFilesParsingResults())->handle();

    expect($link->refresh()->parsing_failed_at)->toBeNull();

    Exceptions::assertNotReported(AiLinkParsingFailedException::class);
});

it('checks on parse jobs submitted at least 5 minutes ago that were not checked in the last 5 minutes', function (string $linkModel): void {
    Queue::fake();

    $dueLink = $linkModel::factory()->parsingInProgress()->create([
        'parsing_started_at' => now()->subMinutes(6),
        'parsing_last_checked_at' => now()->subMinutes(6),
    ]);

    $recentlySubmittedLink = $linkModel::factory()->parsingInProgress()->create([
        'parsing_started_at' => now()->subMinutes(2),
        'parsing_last_checked_at' => now()->subMinutes(2),
    ]);

    $recentlyCheckedLink = $linkModel::factory()->parsingInProgress()->create([
        'parsing_started_at' => now()->subMinutes(30),
        'parsing_last_checked_at' => now()->subMinute(),
    ]);

    // Discard anything pushed by model observers while arranging the records.
    Queue::fake();

    (new FetchAiFilesParsingResults())->handle();

    Queue::assertPushed(FetchAiLinkParsingResults::class, fn (FetchAiLinkParsingResults $job): bool => $job->uniqueId() === $dueLink->getKey());
    Queue::assertNotPushed(FetchAiLinkParsingResults::class, fn (FetchAiLinkParsingResults $job): bool => $job->uniqueId() === $recentlySubmittedLink->getKey());
    Queue::assertNotPushed(FetchAiLinkParsingResults::class, fn (FetchAiLinkParsingResults $job): bool => $job->uniqueId() === $recentlyCheckedLink->getKey());
})->with([
    'customer advisor link' => [CustomerAdvisorLink::class],
    'AI assistant link' => [AiAssistantLink::class],
]);

// TODO: Cleanup Task (llamaparse-link-parsing): remove this test along with the Jina fallback.
it('dispatches the Jina parsing jobs for recent unparsed links while LlamaParse link parsing is inactive', function (): void {
    LlamaParseLinkParsingFeature::deactivate();

    Queue::fake();

    $assistantLink = AiAssistantLink::factory()->create(['parsing_results' => null]);
    $advisorLink = CustomerAdvisorLink::factory()->create(['parsing_results' => null]);

    // Discard anything pushed by model observers while arranging the records.
    Queue::fake();

    (new FetchAiFilesParsingResults())->handle();

    Queue::assertPushed(FetchAiAssistantLinkParsingResults::class, fn (FetchAiAssistantLinkParsingResults $job): bool => $job->uniqueId() === $assistantLink->getKey());
    Queue::assertPushed(FetchCustomerAdvisorLinkParsingResults::class, fn (FetchCustomerAdvisorLinkParsingResults $job): bool => $job->uniqueId() === $advisorLink->getKey());
    Queue::assertNotPushed(SubmitAiLinkForParsing::class);
});
