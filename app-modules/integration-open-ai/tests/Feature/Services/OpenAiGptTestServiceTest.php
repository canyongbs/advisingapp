<?php

use Illuminate\Support\Str;
use OpenAI\Resources\Threads;
use OpenAI\Testing\ClientFake;

use function Tests\asSuperAdmin;

use OpenAI\Resources\Assistants;
use AdvisingApp\Ai\Enums\AiModel;
use OpenAI\Resources\ThreadsRuns;
use AdvisingApp\Ai\Models\AiThread;
use AdvisingApp\Ai\Models\AiMessage;
use OpenAI\Resources\ThreadsMessages;
use AdvisingApp\Ai\Models\AiAssistant;
use AdvisingApp\Ai\Enums\AiApplication;
use OpenAI\Responses\Threads\ThreadResponse;
use OpenAI\Responses\Assistants\AssistantResponse;
use OpenAI\Responses\Threads\ThreadDeleteResponse;
use OpenAI\Responses\Threads\Runs\ThreadRunResponse;
use OpenAI\Responses\Threads\Runs\ThreadRunListResponse;
use OpenAI\Responses\Threads\Messages\ThreadMessageResponse;
use AdvisingApp\IntegrationOpenAi\Services\BaseOpenAiService;
use OpenAI\Responses\Threads\Messages\ThreadMessageListResponse;

it('can create an assistant', function () {
    asSuperAdmin();

    /** @var BaseOpenAiService $service */
    $service = AiModel::OpenAiGptTest->getService();

    /** @var ClientFake $client */
    $client = $service->getClient();

    $client->addResponses([
        AssistantResponse::fake([
            'id' => $assistantId = Str::random(),
        ]),
    ]);

    $assistant = AiAssistant::factory()->make();

    $service->createAssistant($assistant);

    expect($assistant->assistant_id)
        ->toBe($assistantId);

    $client->assertSent(Assistants::class, 1);
});

it('can update an assistant', function () {
    asSuperAdmin();

    /** @var BaseOpenAiService $service */
    $service = AiModel::OpenAiGptTest->getService();

    /** @var ClientFake $client */
    $client = $service->getClient();

    $client->addResponses([
        AssistantResponse::fake(),
    ]);

    $assistant = AiAssistant::factory()
        ->create([
            'application' => AiApplication::PersonalAssistant,
            'assistant_id' => Str::random(),
            'is_default' => true,
            'model' => AiModel::OpenAiGptTest,
        ]);

    $service->updateAssistant($assistant);

    $client->assertSent(Assistants::class, 1);
});

it('can create a thread', function () {
    asSuperAdmin();

    /** @var BaseOpenAiService $service */
    $service = AiModel::OpenAiGptTest->getService();

    /** @var ClientFake $client */
    $client = $service->getClient();

    $client->addResponses([
        ThreadResponse::fake([
            'id' => $threadId = Str::random(),
        ]),
    ]);

    $thread = AiThread::factory()->make();

    $service->createThread($thread);

    expect($thread->thread_id)
        ->toBe($threadId);

    $client->assertSent(Threads::class, 1);
});

it('can delete a thread', function () {
    asSuperAdmin();

    /** @var BaseOpenAiService $service */
    $service = AiModel::OpenAiGptTest->getService();

    /** @var ClientFake $client */
    $client = $service->getClient();

    $client->addResponses([
        ThreadDeleteResponse::fake(),
    ]);

    $thread = AiThread::factory()
        ->make([
            'thread_id' => Str::random(),
        ]);

    $service->deleteThread($thread);

    expect($thread->thread_id)
        ->toBeNull();

    $client->assertSent(Threads::class, 1);
});

it('can send a message', function () {
    asSuperAdmin();

    /** @var BaseOpenAiService $service */
    $service = AiModel::OpenAiGptTest->getService();

    /** @var ClientFake $client */
    $client = $service->getClient();

    $client->addResponses([
        ThreadMessageResponse::fake([
            'id' => $messageId = Str::random(),
        ]),
        ThreadRunResponse::fake([
            'status' => 'completed',
        ]),
        ThreadMessageListResponse::fake([
            'data' => [
                [
                    'content' => [
                        [
                            'text' => [
                                'value' => $responseContent = 'Hello, how can I help you?',
                            ],
                        ],
                    ],
                    'id' => $responseId = Str::random(),
                ],
            ],
        ]),
    ]);

    $message = AiMessage::factory()
        ->for(AiThread::factory()
            ->for(AiAssistant::factory()->state([
                'application' => AiApplication::PersonalAssistant,
                'assistant_id' => Str::random(),
                'is_default' => true,
                'model' => AiModel::OpenAiGptTest,
            ]), 'assistant')
            ->for(auth()->user())
            ->state([
                'thread_id' => Str::random(),
            ]), 'thread')
        ->make();

    $response = $service->sendMessage($message);

    expect($message)
        ->message_id->toBe($messageId);

    expect($response)
        ->content->toBe($responseContent)
        ->message_id->toBe($responseId);

    $client->assertSent(ThreadsMessages::class, 2);
    $client->assertSent(ThreadsRuns::class, 1);
});

it('can retry a message', function () {
    asSuperAdmin();

    /** @var BaseOpenAiService $service */
    $service = AiModel::OpenAiGptTest->getService();

    /** @var ClientFake $client */
    $client = $service->getClient();

    $client->addResponses([
        ThreadRunListResponse::fake([
            'data' => [
                [
                    'status' => 'completed',
                ],
            ],
        ]),
        ThreadMessageResponse::fake([
            'id' => $messageId = Str::random(),
        ]),
        ThreadRunResponse::fake([
            'status' => 'completed',
        ]),
        ThreadMessageListResponse::fake([
            'data' => [
                [
                    'content' => [
                        [
                            'text' => [
                                'value' => $responseContent = 'Hello, how can I help you?',
                            ],
                        ],
                    ],
                    'id' => $responseId = Str::random(),
                ],
            ],
        ]),
    ]);

    $message = AiMessage::factory()
        ->for(AiThread::factory()
            ->for(AiAssistant::factory()->state([
                'application' => AiApplication::PersonalAssistant,
                'assistant_id' => Str::random(),
                'is_default' => true,
                'model' => AiModel::OpenAiGptTest,
            ]), 'assistant')
            ->for(auth()->user())
            ->state([
                'thread_id' => Str::random(),
            ]), 'thread')
        ->make();

    $response = $service->retryMessage($message);

    expect($message)
        ->message_id->toBe($messageId);

    expect($response)
        ->content->toBe($responseContent)
        ->message_id->toBe($responseId);

    $client->assertSent(ThreadsRuns::class, 2);
    $client->assertSent(ThreadsMessages::class, 2);
});

it('can await the response of a previous run instead of sending a message again when retrying', function () {
    asSuperAdmin();

    /** @var BaseOpenAiService $service */
    $service = AiModel::OpenAiGptTest->getService();

    /** @var ClientFake $client */
    $client = $service->getClient();

    $client->addResponses([
        ThreadRunListResponse::fake([
            'data' => [
                [
                    'status' => 'queued',
                ],
            ],
        ]),
        ThreadRunResponse::fake([
            'status' => 'completed',
        ]),
        ThreadMessageListResponse::fake([
            'data' => [
                [
                    'content' => [
                        [
                            'text' => [
                                'value' => $responseContent = 'Hello, how can I help you?',
                            ],
                        ],
                    ],
                    'id' => $responseId = Str::random(),
                ],
            ],
        ]),
    ]);

    $message = AiMessage::factory()
        ->for(AiThread::factory()
            ->for(AiAssistant::factory()->state([
                'application' => AiApplication::PersonalAssistant,
                'assistant_id' => Str::random(),
                'is_default' => true,
                'model' => AiModel::OpenAiGptTest,
            ]), 'assistant')
            ->for(auth()->user())
            ->state([
                'thread_id' => Str::random(),
            ]), 'thread')
        ->make([
            'message_id' => Str::random(),
        ]);

    $response = $service->retryMessage($message);

    expect($response)
        ->content->toBe($responseContent)
        ->message_id->toBe($responseId);

    $client->assertSent(ThreadsRuns::class, 2);
    $client->assertSent(ThreadsMessages::class, 1);
});

it('can create a run if one does not exist without sending the message again when retrying', function () {
    asSuperAdmin();

    /** @var BaseOpenAiService $service */
    $service = AiModel::OpenAiGptTest->getService();

    /** @var ClientFake $client */
    $client = $service->getClient();

    $client->addResponses([
        ThreadRunListResponse::fake([
            'data' => [],
        ]),
        ThreadRunResponse::fake([
            'status' => 'completed',
        ]),
        ThreadMessageListResponse::fake([
            'data' => [
                [
                    'content' => [
                        [
                            'text' => [
                                'value' => $responseContent = 'Hello, how can I help you?',
                            ],
                        ],
                    ],
                    'id' => $responseId = Str::random(),
                ],
            ],
        ]),
    ]);

    $message = AiMessage::factory()
        ->for(AiThread::factory()
            ->for(AiAssistant::factory()->state([
                'application' => AiApplication::PersonalAssistant,
                'assistant_id' => Str::random(),
                'is_default' => true,
                'model' => AiModel::OpenAiGptTest,
            ]), 'assistant')
            ->for(auth()->user())
            ->state([
                'thread_id' => Str::random(),
            ]), 'thread')
        ->make([
            'message_id' => Str::random(),
        ]);

    $response = $service->retryMessage($message);

    expect($response)
        ->content->toBe($responseContent)
        ->message_id->toBe($responseId);

    $client->assertSent(ThreadsRuns::class, 2);
    $client->assertSent(ThreadsMessages::class, 1);
});
