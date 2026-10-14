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

use AdvisingApp\Ai\Jobs\Advisors\FetchAiAssistantFileParsingResults;
use AdvisingApp\Ai\Jobs\AiAssistants\FetchAiAssistantLinkParsingResults;
use AdvisingApp\Ai\Jobs\CustomerAdvisors\FetchCustomerAdvisorFileParsingResults;
use AdvisingApp\Ai\Jobs\CustomerAdvisors\FetchCustomerAdvisorLinkParsingResults;
use AdvisingApp\Ai\Jobs\FetchAiFilesParsingResults;
use AdvisingApp\Ai\Models\AiAssistant;
use AdvisingApp\Ai\Models\AiAssistantFile;
use AdvisingApp\Ai\Models\AiAssistantLink;
use AdvisingApp\Ai\Models\CustomerAdvisorFile;
use AdvisingApp\Ai\Models\CustomerAdvisorLink;
use Illuminate\Support\Facades\Queue;

it('dispatches a parsing results job for each recent unparsed file and link', function () {
    Queue::fake();

    $assistantFile = AiAssistantFile::factory()->create(['assistant_id' => AiAssistant::factory(), 'parsing_results' => null]);
    $assistantLink = AiAssistantLink::factory()->create(['parsing_results' => null]);
    $advisorFile = CustomerAdvisorFile::factory()->create(['parsing_results' => null]);
    $advisorLink = CustomerAdvisorLink::factory()->create(['parsing_results' => null]);

    // Discard anything pushed by model observers while arranging the records.
    Queue::fake();

    (new FetchAiFilesParsingResults())->handle();

    Queue::assertPushed(FetchAiAssistantFileParsingResults::class, fn (FetchAiAssistantFileParsingResults $job) => $job->uniqueId() === $assistantFile->getKey());
    Queue::assertPushed(FetchAiAssistantLinkParsingResults::class, fn (FetchAiAssistantLinkParsingResults $job) => $job->uniqueId() === $assistantLink->getKey());
    Queue::assertPushed(FetchCustomerAdvisorFileParsingResults::class, fn (FetchCustomerAdvisorFileParsingResults $job) => $job->uniqueId() === $advisorFile->getKey());
    Queue::assertPushed(FetchCustomerAdvisorLinkParsingResults::class, fn (FetchCustomerAdvisorLinkParsingResults $job) => $job->uniqueId() === $advisorLink->getKey());
});

it('does not dispatch for files and links that are already parsed or older than an hour', function () {
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
