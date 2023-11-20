<?php

use App\Models\User;

use function Pest\Laravel\{actingAs};

use Assist\IntegrationAI\Events\AIPromptInitiated;
use Assist\IntegrationAI\Client\Contracts\AIChatClient;
use Assist\Assistant\Services\AIInterface\Enums\AIChatMessageFrom;
use Assist\Assistant\Services\AIInterface\DataTransferObjects\Chat;
use Assist\Assistant\Services\AIInterface\DataTransferObjects\ChatMessage;

it('will return a streamed response of strings when prompted', function () {
    $user = User::factory()->create();

    actingAs($user);

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

    actingAs($user);

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
