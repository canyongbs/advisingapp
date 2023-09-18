<?php

use App\Models\User;
use Assist\IntegrationAI\Events\AIPromptInitiated;
use Assist\IntegrationAI\Client\Contracts\AIChatClient;
use Assist\IntegrationAI\Client\Playground\AzureOpenAI;
use Assist\IntegrationAI\Exceptions\TokensExceededException;
use Assist\Assistant\Services\AIInterface\Enums\AIChatMessageFrom;
use Assist\Assistant\Services\AIInterface\DataTransferObjects\Chat;
use Assist\IntegrationAI\Client\Playground\MockStreamResponseGenerator;
use Assist\Assistant\Services\AIInterface\DataTransferObjects\ChatMessage;

it('will return a streamed response of strings when prompted', function () {
    $user = User::factory()->create();

    $this->actingAs($user);

    // Given that we initiate a Chat
    $chat = new Chat(
        id: null,
        messages: ChatMessage::collection([]),
    );

    $chat->messages[] = new ChatMessage(
        message: 'Hello',
        from: AIChatMessageFrom::User,
    );

    $client = app(AIChatClient::class);

    $client->ask($chat, function ($response) {
        expect($response)->toBeString();
    });
});

it('will dispatch an event when a prompt is initiated', function () {
    Event::fake([AIPromptInitiated::class]);

    $user = User::factory()->create();

    $this->actingAs($user);

    // Given that we initiate a Chat
    $chat = new Chat(
        id: null,
        messages: ChatMessage::collection([]),
    );

    $chat->messages[] = new ChatMessage(
        message: 'Hello',
        from: AIChatMessageFrom::User,
    );

    $client = app(AIChatClient::class);

    $client->ask($chat, function ($response) {
        expect($response)->toBeString();
    });

    Event::assertDispatched(AIPromptInitiated::class);
});

// TODO Fix this test
it('will throw an exception if the client response warrants one', function () {
    $this->markTestSkipped();

    $user = User::factory()->create();

    $this->actingAs($user);

    // Given that we initiate a Chat
    $chat = new Chat(
        id: null,
        messages: ChatMessage::collection([]),
    );

    $chat->messages[] = new ChatMessage(
        message: 'Hello',
        from: AIChatMessageFrom::User,
    );

    $this->partialMock(AzureOpenAI::class, function ($mock) {
        $mock
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('generateFakeText')
            ->once()
            ->andReturn(
                resolve(MockStreamResponseGenerator::class)
                    ->withLengthError()
                    ->generateFakeStreamResponse()
            );
    });

    $client->ask($chat, function ($response) {});
})->throws(TokensExceededException::class);
