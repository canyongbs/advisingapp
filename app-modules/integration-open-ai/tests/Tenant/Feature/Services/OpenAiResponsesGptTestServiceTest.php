<?php

/*
<COPYRIGHT>

    Copyright © 2016-2025, Canyon GBS LLC. All rights reserved.

    Advising App™ is licensed under the Elastic License 2.0. For more details,
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
    - Canyon GBS LLC respects the intellectual property rights of others and expects the
      same in return. Canyon GBS™ and Advising App™ are registered trademarks of
      Canyon GBS LLC, and we are committed to enforcing and protecting our trademarks
      vigorously.
    - The software solution, including services, infrastructure, and code, is offered as a
      Software as a Service (SaaS) by Canyon GBS LLC.
    - Use of this software implies agreement to the license terms and conditions as stated
      in the Elastic License 2.0.

    For more information or inquiries please visit our website at
    https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

use AdvisingApp\Ai\Enums\AiAssistantApplication;
use AdvisingApp\Ai\Enums\AiModel;
use AdvisingApp\Ai\Models\AiAssistant;
use AdvisingApp\Ai\Models\AiMessage;
use AdvisingApp\Ai\Models\AiMessageFile;
use AdvisingApp\Ai\Models\AiThread;
use AdvisingApp\IntegrationOpenAi\Models\OpenAiVectorStore;
use AdvisingApp\IntegrationOpenAi\Services\OpenAiResponsesGptTestService;
use AdvisingApp\Report\Enums\TrackedEventType;
use AdvisingApp\Report\Jobs\RecordTrackedEvent;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Str;
use Prism\Prism\Enums\FinishReason;
use Prism\Prism\Prism;
use Prism\Prism\Testing\TextResponseFake;

use function Tests\asSuperAdmin;

beforeEach(function () {
    Http::preventStrayRequests();
});

it('can send a message', function () {
    Queue::fake();

    asSuperAdmin();

    $service = app(OpenAiResponsesGptTestService::class);

    $message = AiMessage::factory()
        ->for(AiThread::factory()
            ->for(AiAssistant::factory()->state([
                'application' => AiAssistantApplication::PersonalAssistant,
                'is_default' => true,
                'model' => AiModel::OpenAiGptTest,
            ]), 'assistant')
            ->for(auth()->user()), 'thread')
        ->make();

    $originalMessageContent = $message->content;

    Prism::fake([
        TextResponseFake::make()
            ->withText(strrev($message->content))
            ->withFinishReason(FinishReason::Stop),
    ]);

    $chunkedResponseContent = '';
    $finalResponseContent = null;

    $stream = $service->sendMessage($message, [], function (AiMessage $response) use (&$finalResponseContent) {
        $finalResponseContent = $response->content;
    });

    foreach ($stream() as $chunk) {
        expect($chunk)
            ->toBeJson();

        $chunkedResponseContent .= base64_decode(json_decode($chunk)->content);
    }

    expect($finalResponseContent)
        ->toBe($chunkedResponseContent)
        ->toBe(strrev($originalMessageContent));

    expect(Queue::pushed(RecordTrackedEvent::class)) /** @phpstan-ignore argument.templateType */
        ->toHaveCount(1)
        ->each
        ->toHaveProperties(['type' => TrackedEventType::AiExchange]);
});

it('can complete a message response', function () {
    Queue::fake();

    asSuperAdmin();

    $service = app(OpenAiResponsesGptTestService::class);

    $message = AiMessage::factory()
        ->for(AiThread::factory()
            ->for(AiAssistant::factory()->state([
                'application' => AiAssistantApplication::PersonalAssistant,
                'is_default' => true,
                'model' => AiModel::OpenAiGptTest,
            ]), 'assistant'), 'thread')
        ->make(['user_id' => null]);

    $originalMessageContent = $message->content;

    Prism::fake([
        TextResponseFake::make()
            ->withText(strrev($message->content))
            ->withFinishReason(FinishReason::Stop),
    ]);

    $chunkedResponseContent = '';
    $finalResponseContent = null;

    $stream = $service->completeResponse($message, function (AiMessage $response) use (&$finalResponseContent) {
        $finalResponseContent = $response->content;
    });

    foreach ($stream() as $chunk) {
        expect($chunk)
            ->toBeJson();

        $chunkedResponseContent .= base64_decode(json_decode($chunk)->content);
    }

    expect($finalResponseContent)
        ->toBe($originalMessageContent . strrev($originalMessageContent));

    expect($chunkedResponseContent)
        ->toBe(strrev($originalMessageContent));

    expect(Queue::pushed(RecordTrackedEvent::class)) /** @phpstan-ignore argument.templateType */
        ->toHaveCount(1)
        ->each
        ->toHaveProperties(['type' => TrackedEventType::AiExchange]);
});

it('can retry a message', function () {
    Queue::fake();

    asSuperAdmin();

    $service = app(OpenAiResponsesGptTestService::class);

    $message = AiMessage::factory()
        ->for(AiThread::factory()
            ->for(AiAssistant::factory()->state([
                'application' => AiAssistantApplication::PersonalAssistant,
                'is_default' => true,
                'model' => AiModel::OpenAiGptTest,
            ]), 'assistant')
            ->for(auth()->user()), 'thread')
        ->make();

    $originalMessageContent = $message->content;

    Prism::fake([
        TextResponseFake::make()
            ->withText(strrev($message->content))
            ->withFinishReason(FinishReason::Stop),
    ]);

    $chunkedResponseContent = '';
    $finalResponseContent = null;

    $stream = $service->retryMessage($message, [], function (AiMessage $response) use (&$finalResponseContent) {
        $finalResponseContent = $response->content;
    });

    foreach ($stream() as $chunk) {
        expect($chunk)
            ->toBeJson();

        $chunkedResponseContent .= base64_decode(json_decode($chunk)->content);
    }

    expect($finalResponseContent)
        ->toBe($chunkedResponseContent)
        ->toBe(strrev($originalMessageContent));

    expect(Queue::pushed(RecordTrackedEvent::class)) /** @phpstan-ignore argument.templateType */
        ->toHaveCount(1)
        ->each
        ->toHaveProperties(['type' => TrackedEventType::AiExchange]);
});

it('can complete a prompt', function () {
    Queue::fake();

    asSuperAdmin();

    $service = app(OpenAiResponsesGptTestService::class);

    Prism::fake([
        TextResponseFake::make()
            ->withText($response = Str::random())
            ->withFinishReason(FinishReason::Stop),
    ]);

    expect($service->complete(Str::random(), Str::random()))
        ->toBe($response);

    expect(Queue::pushed(RecordTrackedEvent::class)) /** @phpstan-ignore argument.templateType */
        ->toHaveCount(1)
        ->each
        ->toHaveProperties(['type' => TrackedEventType::AiExchange]);
});

it('can fetch a valid previous response ID for a message', function () {
    Http::fake([
        '*/responses/*' => Http::response(['id' => 'resp_12345'], 200),
    ]);

    asSuperAdmin();

    $service = app(OpenAiResponsesGptTestService::class);

    $previousAssistantResponse = AiMessage::factory()
        ->for(AiThread::factory()
            ->for(AiAssistant::factory()->state([
                'application' => AiAssistantApplication::PersonalAssistant,
                'is_default' => true,
                'model' => AiModel::OpenAiGptTest,
            ]), 'assistant'), 'thread')
        ->create([
            'message_id' => 'resp_12345',
            'user_id' => null,
        ]);

    $message = AiMessage::factory()
        ->for($previousAssistantResponse->thread, 'thread')
        ->make();

    $previousResponseId = $service->getMessagePreviousResponseId($message);

    Http::assertSentCount(1);

    expect($previousResponseId)
        ->toBe('resp_12345');
});

it('can discard an invalid previous response ID for a message', function () {
    Http::fake([
        '*/responses/*' => Http::response(null, 404),
    ]);

    asSuperAdmin();

    $service = app(OpenAiResponsesGptTestService::class);

    $previousAssistantResponse = AiMessage::factory()
        ->for(AiThread::factory()
            ->for(AiAssistant::factory()->state([
                'application' => AiAssistantApplication::PersonalAssistant,
                'is_default' => true,
                'model' => AiModel::OpenAiGptTest,
            ]), 'assistant'), 'thread')
        ->create([
            'message_id' => 'resp_12345',
            'user_id' => null,
        ]);

    $message = AiMessage::factory()
        ->for($previousAssistantResponse->thread, 'thread')
        ->make();

    $previousResponseId = $service->getMessagePreviousResponseId($message);

    Http::assertSentCount(1);

    expect($previousResponseId)
        ->toBeNull();
});

it('can confirm that a file is ready if it has a stored timestamp', function () {
    $service = app(OpenAiResponsesGptTestService::class);

    $file = AiMessageFile::factory()
        ->has(OpenAiVectorStore::factory()->state([
            'deployment_hash' => $service->getDeploymentHash(),
        ]))
        ->create();

    expect($file->openAiVectorStore->ready_until->isFuture())
        ->toBeTrue();

    expect($service->isFileReady($file))
        ->toBeTrue();
});

it('can confirm that a file is not ready if it is still processing', function () {
    Http::fake([
        '*/vector_stores/*' => Http::response([
            'status' => 'in_progress',
            'file_counts' => [
                'in_progress' => 1,
            ],
        ], 200),
    ]);

    $service = app(OpenAiResponsesGptTestService::class);

    $file = AiMessageFile::factory()
        ->has(OpenAiVectorStore::factory()->state([
            'deployment_hash' => $service->getDeploymentHash(),
            'ready_until' => null,
        ]))
        ->create();

    expect($service->isFileReady($file))
        ->toBeFalse();
});

it('can confirm that a file is ready if all files are finished processing', function () {
    Http::fake([
        '*/vector_stores/*' => Http::response([
            'status' => 'completed',
            'file_counts' => [
                'completed' => 1,
                'total' => 1,
            ],
            'expires_at' => ($expiresAt = now()->addWeek())->getTimestamp(),
        ], 200),
    ]);

    $service = app(OpenAiResponsesGptTestService::class);

    $file = AiMessageFile::factory()
        ->has(OpenAiVectorStore::factory()->state([
            'deployment_hash' => $service->getDeploymentHash(),
            'ready_until' => null,
            'vector_store_file_id' => null,
        ]))
        ->create();

    expect($service->isFileReady($file))
        ->toBeTrue();

    expect($file->openAiVectorStore->ready_until->toDateTimeString())
        ->toBe($expiresAt->subHours(2)->toDateTimeString());
});

it('can delete an existing vector store file to ensure storage space is used efficiently', function () {
    Http::fake([
        '*/vector_stores/*' => Http::response([
            'status' => 'completed',
            'file_counts' => [
                'completed' => 1,
                'total' => 1,
            ],
            'expires_at' => now()->addWeek()->getTimestamp(),
        ], 200),
        '*/files/*' => Http::response(null, 200),
    ]);

    $service = app(OpenAiResponsesGptTestService::class);

    $file = AiMessageFile::factory()
        ->has(OpenAiVectorStore::factory()->state([
            'deployment_hash' => $service->getDeploymentHash(),
            'ready_until' => null,
        ]))
        ->create();

    expect($file->openAiVectorStore->vector_store_file_id)
        ->not->toBeNull();

    expect($service->isFileReady($file))
        ->toBeTrue();

    expect($file->openAiVectorStore->refresh()->vector_store_file_id)
        ->toBeNull();
});

it('can upload a file and create a new vector store', function () {
    Http::fake([
        '*/files/*' => Http::response(null, 404),
        '*/files*' => Http::response([
            'id' => $fileId = fake()->uuid(),
        ], 200),
        '*/vector_stores*' => Http::response([
            'id' => $vectorStoreId = fake()->uuid(),
        ], 200),
    ]);

    $service = app(OpenAiResponsesGptTestService::class);

    $file = AiMessageFile::factory()
        ->has(OpenAiVectorStore::factory()->state([
            'deployment_hash' => $service->getDeploymentHash(),
            'ready_until' => null,
            'vector_store_id' => null,
        ]))
        ->create();

    expect($service->isFileReady($file))
        ->toBeFalse();

    expect($file->openAiVectorStore->refresh())
        ->vector_store_file_id->toBe($fileId)
        ->vector_store_id->toBe($vectorStoreId);
});

it('can get ready vector store IDs from an array of files', function () {
    $service = app(OpenAiResponsesGptTestService::class);

    $file1 = AiMessageFile::factory()
        ->has(OpenAiVectorStore::factory()->state([
            'deployment_hash' => $service->getDeploymentHash(),
            'ready_until' => now()->addDays(2),
        ]))
        ->create();

    $file2 = AiMessageFile::factory()
        ->has(OpenAiVectorStore::factory()->state([
            'deployment_hash' => $service->getDeploymentHash(),
            'ready_until' => now()->addDays(3),
        ]))
        ->create();

    $file3 = AiMessageFile::factory()
        ->has(OpenAiVectorStore::factory()->state([
            'deployment_hash' => $service->getDeploymentHash(),
            'ready_until' => now()->subDays(1),
        ]))
        ->create();

    $files = [$file1, $file2, $file3];

    $readyVectorStoreIds = $service->getReadyVectorStoreIds($files);

    expect($readyVectorStoreIds)
        ->toBeArray()
        ->toHaveCount(2)
        ->toContain($file1->openAiVectorStore->vector_store_id)
        ->toContain($file2->openAiVectorStore->vector_store_id);
});
