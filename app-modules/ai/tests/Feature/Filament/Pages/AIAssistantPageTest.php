<?php

/*
<COPYRIGHT>

    Copyright © 2016-2024, Canyon GBS LLC. All rights reserved.

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

use App\Models\User;
use Livewire\Livewire;
use Illuminate\Support\Str;

use function Tests\asSuperAdmin;

use AdvisingApp\Ai\Models\Prompt;
use AdvisingApp\Team\Models\Team;
use App\Filament\Pages\Dashboard;
use Illuminate\Support\Facades\Bus;
use AdvisingApp\Ai\Models\PromptUse;
use AdvisingApp\Ai\Models\PromptUpvote;
use AdvisingApp\Assistant\Models\AiAssistant;
use AdvisingApp\Ai\Enums\AssistantChatShareVia;
use AdvisingApp\Ai\Jobs\ShareAssistantChatsJob;
use AdvisingApp\Assistant\Models\AssistantChat;
use AdvisingApp\Ai\Enums\AssistantChatShareWith;
use AdvisingApp\Assistant\Enums\AiAssistantType;
use AdvisingApp\Authorization\Enums\LicenseType;
use AdvisingApp\Consent\Models\ConsentAgreement;

use function Spatie\PestPluginTestTime\testTime;

use AdvisingApp\Consent\Enums\ConsentAgreementType;
use AdvisingApp\Assistant\Models\AssistantChatFolder;
use AdvisingApp\Assistant\Models\AssistantChatMessage;
use AdvisingApp\Assistant\Filament\Pages\PersonalAssistant;
use AdvisingApp\IntegrationAI\Client\Contracts\AiChatClient;
use AdvisingApp\IntegrationAI\Client\Playground\AzureOpenAI;
use AdvisingApp\IntegrationAI\Exceptions\ContentFilterException;
use AdvisingApp\IntegrationAI\Exceptions\TokensExceededException;
use OpenAI\Testing\Responses\Fixtures\Threads\ThreadResponseFixture;
use AdvisingApp\Assistant\Services\AIInterface\Enums\AIChatMessageFrom;
use AdvisingApp\Assistant\Services\AIInterface\DataTransferObjects\Chat;
use AdvisingApp\Assistant\Services\AIInterface\DataTransferObjects\ChatMessage;

use function Pest\Laravel\{actingAs,
    assertDatabaseHas,
    assertDatabaseMissing,
    assertNotSoftDeleted,
    assertSoftDeleted,
    mock};

$setUp = function (
    bool $hasUserConsented = true,
) {
    $consentAgreement = ConsentAgreement::factory()->create([
        'type' => ConsentAgreementType::AzureOpenAI,
    ]);

    $user = User::factory()->licensed(LicenseType::ConversationalAi)->create();
    $user->givePermissionTo('assistant.access');

    actingAs($user);

    if ($hasUserConsented) {
        $user->consentTo($consentAgreement);
    }

    AiAssistant::factory()->create([
        'type' => AiAssistantType::Default,
    ]);

    $chat = AssistantChat::factory()
        ->for($user)
        ->has(AssistantChatMessage::factory()->count(5), 'messages')
        ->create();

    return ['user' => $user, 'consentAgreement' => $consentAgreement, 'chat' => $chat];
};

it('renders successfully', function () {
    ConsentAgreement::factory()->create([
        'type' => ConsentAgreementType::AzureOpenAI,
    ]);

    asSuperAdmin();

    Livewire::test(PersonalAssistant::class)
        ->assertStatus(200);
});

it('is properly gated with access control', function () {
    ConsentAgreement::factory()->create([
        'type' => ConsentAgreementType::AzureOpenAI,
    ]);

    $user = User::factory()->licensed(LicenseType::ConversationalAi)->create();

    actingAs($user);

    Livewire::test(PersonalAssistant::class)
        ->assertStatus(403);

    $user->givePermissionTo('assistant.access');

    Livewire::test(PersonalAssistant::class)
        ->assertStatus(200);
});

it('will show a consent modal if the user has not yet agreed to the terms and conditions of use', function () use ($setUp) {
    ['consentAgreement' => $consentAgreement] = $setUp(
        hasUserConsented: false,
    );

    Livewire::test(PersonalAssistant::class)
        ->call('determineIfConsentWasGiven')
        ->assertViewHas('consentedToTerms', false)
        ->assertSee($consentAgreement->title)
        ->assertSeeHtml(str($consentAgreement->description)->markdown()->sanitizeHtml()->toHtmlString())
        ->assertSeeHtml(str($consentAgreement->body)->markdown()->sanitizeHtml()->toHtmlString());
});

it('will show the AI Assistant interface if the user has agreed to the terms and conditions of use', function () use ($setUp) {
    ['consentAgreement' => $consentAgreement] = $setUp();

    Livewire::test(PersonalAssistant::class)
        ->call('determineIfConsentWasGiven')
        ->assertViewHas('consentedToTerms', true)
        ->assertDontSee($consentAgreement->title)
        ->assertDontSee($consentAgreement->description)
        ->assertDontSee($consentAgreement->body);
});

it('will redirect the user back to the dashboard if they dismiss the consent modal', function () use ($setUp) {
    $setUp(
        hasUserConsented: false,
    );

    Livewire::test(PersonalAssistant::class)
        ->call('denyConsent')
        ->assertRedirect(Dashboard::getUrl());
});

it('will allow a user to access the AI Assistant interface if they agree to the terms and conditions of use', function () use ($setUp) {
    ['user' => $user, 'consentAgreement' => $consentAgreement] = $setUp(
        hasUserConsented: false,
    );

    expect($user->hasConsentedTo($consentAgreement))->toBeFalse();

    $livewire = Livewire::test(PersonalAssistant::class);

    $livewire
        ->call('determineIfConsentWasGiven')
        ->assertViewHas('consentedToTerms', false)
        ->assertSee($consentAgreement->title)
        ->assertSeeHtml(str($consentAgreement->description)->markdown()->sanitizeHtml()->toHtmlString())
        ->assertSeeHtml(str($consentAgreement->body)->markdown()->sanitizeHtml()->toHtmlString());

    $livewire
        ->set('consentedToTerms', true)
        ->call('confirmConsent')
        ->assertDontSee($consentAgreement->title)
        ->assertDontSee($consentAgreement->description)
        ->assertDontSee($consentAgreement->body);

    expect($user->hasConsentedTo($consentAgreement))->toBeTrue();
});

it('will automatically set the current chat when it does not have a folder', function () use ($setUp) {
    ['chat' => $chat] = $setUp();

    $livewire = Livewire::test(PersonalAssistant::class);

    expect($livewire->chat->toArray())->toEqual(
        (new Chat(
            id: $chat->id,
            messages: ChatMessage::collection($chat->messages),
            assistantId: $chat->assistant->assistant_id,
            threadId: null,
        ))->toArray(),
    );
});

it('will automatically set the current chat to the most recent without a folder', function () use ($setUp) {
    ['user' => $user] = $setUp();

    $newerChat = AssistantChat::factory()
        ->for($user)
        ->has(AssistantChatMessage::factory()->count(5), 'messages')
        ->create([
            'created_at' => now()->addMinute(),
        ]);

    $livewire = Livewire::test(PersonalAssistant::class);

    expect($livewire->chat->toArray())->toEqual(
        (new Chat(
            id: $newerChat->id,
            messages: ChatMessage::collection($newerChat->messages),
            assistantId: $newerChat->assistant->assistant_id,
            threadId: null,
        ))->toArray(),
    );
});

it('will not automatically set the current chat to one with a folder', function () use ($setUp) {
    ['user' => $user, 'chat' => $chat] = $setUp();

    $chat->folder()->associate(AssistantChatFolder::factory()->for($user)->create());
    $chat->save();

    $livewire = Livewire::test(PersonalAssistant::class);

    expect($livewire->chat->toArray())->toEqual(
        (new Chat(
            id: null,
            messages: ChatMessage::collection([]),
            assistantId: $chat->assistant->assistant_id,
            threadId: null,
        ))->toArray(),
    );
});

it('will not automatically set the current chat to one belonging to another user', function () use ($setUp) {
    ['chat' => $chat] = $setUp();

    $chat->user()->associate(User::factory()->create());
    $chat->save();

    $livewire = Livewire::test(PersonalAssistant::class);

    expect($livewire->chat->toArray())
        ->toEqual(
            (new Chat(
                id: null,
                messages: ChatMessage::collection([]),
                assistantId: $chat->assistant->assistant_id,
                threadId: null,
            ))->toArray(),
        );
});

it('can send message to an existing chat', function () use ($setUp) {
    ['chat' => $chat] = $setUp();

    Livewire::test(PersonalAssistant::class)
        ->set('showCurrentResponse', false)
        ->set('renderError', true)
        ->set('error', Str::random())
        ->set('message', $message = AssistantChatMessage::factory()->make()->message)
        ->call('sendMessage')
        ->assertSet('showCurrentResponse', true)
        ->assertSet('renderError', false)
        ->assertSet('error', null)
        ->assertSet('prompt', $message)
        ->assertSet('message', null);

    assertDatabaseHas(AssistantChatMessage::class, [
        'assistant_chat_id' => $chat->getKey(),
        'message' => $message,
        'from' => AIChatMessageFrom::User,
    ]);
});

it('can send message to a new chat', function () use ($setUp) {
    ['chat' => $chat] = $setUp();

    $chat->delete();

    testTime()->freeze();
    $createdAt = now()->toDateTimeString();

    $livewire = Livewire::test(PersonalAssistant::class)
        ->set('showCurrentResponse', false)
        ->set('renderError', true)
        ->set('error', Str::random())
        ->set('message', $message = AssistantChatMessage::factory()->make()->message)
        ->call('sendMessage')
        ->assertHasNoErrors()
        ->assertSet('showCurrentResponse', true)
        ->assertSet('renderError', false)
        ->assertSet('error', null)
        ->assertSet('prompt', $message)
        ->assertSet('message', null);

    expect($livewire->chat->toArray())
        ->toEqual(
            (new Chat(
                id: null,
                messages: ChatMessage::collection([
                    new ChatMessage(
                        message: $message,
                        from: AIChatMessageFrom::User,
                        created_at: $createdAt,
                    ),
                ]),
                assistantId: $chat->assistant->assistant_id,
                threadId: null,
            ))->toArray(),
        );
});

it('can not send a blank message', function () use ($setUp) {
    $setUp();

    Livewire::test(PersonalAssistant::class)
        ->set('message', null)
        ->call('sendMessage')
        ->assertHasErrors(['message' => 'required']);
});

it('can ask the AI chat client in an existing chat', function () use ($setUp) {
    ['chat' => $chat] = $setUp();

    $aiChatClient = mock(AiChatClient::class, fn () => AzureOpenAI::class);
    $aiChatClient->expects('provideDynamicContext')->once()->andReturnSelf();
    $aiChatClient->expects('createThread')->once()->andReturn(ThreadResponseFixture::class);
    $aiChatClient->expects('ask')->once()->andReturn($response = AssistantChatMessage::factory()->make()->message);

    Livewire::test(PersonalAssistant::class)
        ->set('showCurrentResponse', true)
        ->assertSet('currentResponse', null)
        ->call('ask')
        ->assertSet('renderError', false)
        ->assertSet('showCurrentResponse', false)
        ->assertSet('currentResponse', null);

    assertDatabaseHas(AssistantChatMessage::class, [
        'assistant_chat_id' => $chat->getKey(),
        'message' => $response,
        'from' => AIChatMessageFrom::Assistant,
    ]);
})->skip();

it('can ask the AI chat client in a new chat', function () use ($setUp) {
    ['chat' => $chat] = $setUp();

    $chat->delete();

    $aiChatClient = mock(AiChatClient::class, fn () => AzureOpenAI::class);
    $aiChatClient->expects('provideDynamicContext')->once()->andReturnSelf();
    $aiChatClient->expects('createThread')->once()->andReturn(ThreadResponseFixture::class);
    $aiChatClient->expects('ask')->once()->andReturn($response = AssistantChatMessage::factory()->make()->message);

    $livewire = Livewire::test(PersonalAssistant::class)
        ->set('showCurrentResponse', true)
        ->assertSet('currentResponse', null)
        ->call('ask')
        ->assertSet('renderError', false)
        ->assertSet('showCurrentResponse', false)
        ->assertSet('currentResponse', null);

    expect($livewire->chat->toArray())
        ->toEqual(
            (new Chat(
                id: null,
                messages: ChatMessage::collection([
                    new ChatMessage(
                        message: $response,
                        from: AIChatMessageFrom::Assistant,
                    ),
                ]),
                assistantId: $chat->assistant->assistant_id,
                threadId: null,
            ))->toArray(),
        );
})->skip();

it('can ask the AI chat client and render a content filter error', function () use ($setUp) {
    ['chat' => $chat] = $setUp();

    $chat->delete();

    $aiChatClient = mock(AiChatClient::class, fn () => AzureOpenAI::class);
    $aiChatClient->expects('provideDynamicContext')->once()->andReturnSelf();
    $aiChatClient->expects('createThread')->once()->andReturn(ThreadResponseFixture::class);
    $aiChatClient->expects('ask')->once()->andThrow(new ContentFilterException($error = Str::random()));

    Livewire::test(PersonalAssistant::class)
        ->set('showCurrentResponse', true)
        ->assertSet('currentResponse', null)
        ->call('ask')
        ->assertSet('renderError', true)
        ->assertSet('error', $error)
        ->assertSet('showCurrentResponse', false)
        ->assertSet('currentResponse', null);
})->skip();

it('can ask the AI chat client and render a tokens exceeded error', function () use ($setUp) {
    ['chat' => $chat] = $setUp();

    $chat->delete();

    $aiChatClient = mock(AiChatClient::class, fn () => AzureOpenAI::class);
    $aiChatClient->expects('provideDynamicContext')->once()->andReturnSelf();
    $aiChatClient->expects('ask')->once()->andThrow(new TokensExceededException($error = Str::random()));

    Livewire::test(PersonalAssistant::class)
        ->set('showCurrentResponse', true)
        ->assertSet('currentResponse', null)
        ->call('ask')
        ->assertSet('renderError', true)
        ->assertSet('error', $error)
        ->assertSet('showCurrentResponse', false)
        ->assertSet('currentResponse', null);
})->skip();

it('can save chats', function () use ($setUp) {
    ['user' => $user, 'chat' => $chat] = $setUp();

    $chat->delete();

    Livewire::test(PersonalAssistant::class)
        ->set('message', $message = AssistantChatMessage::factory()->make()->message)
        ->call('sendMessage')
        ->callAction('saveChat', [
            'name' => $name = Str::random(),
        ])
        ->assertHasNoActionErrors();

    assertDatabaseHas(AssistantChat::class, [
        'user_id' => $user->getKey(),
        'name' => $name,
    ]);

    $chat = AssistantChat::query()->latest()->first();

    assertDatabaseHas(AssistantChatMessage::class, [
        'assistant_chat_id' => $chat->getKey(),
        'message' => $message,
        'from' => AIChatMessageFrom::User,
    ]);
});

it('can save chats into a folder', function () use ($setUp) {
    ['user' => $user, 'chat' => $chat] = $setUp();

    $chat->delete();

    $folder = AssistantChatFolder::factory()
        ->for($user)
        ->create();

    testTime()->freeze();
    $createdAt = now()->toDateTimeString();

    $livewire = Livewire::test(PersonalAssistant::class)
        ->set('message', $message = AssistantChatMessage::factory()->make()->message)
        ->call('sendMessage')
        ->callAction('saveChat', [
            'name' => $name = Str::random(),
            'folder' => $folder->getKey(),
        ])
        ->assertHasNoActionErrors();

    assertDatabaseHas(AssistantChat::class, [
        'user_id' => $user->getKey(),
        'name' => $name,
        'assistant_chat_folder_id' => $folder->getKey(),
    ]);

    $chat = AssistantChat::query()->latest()->first();

    expect($livewire->chat->toArray())
        ->toEqual(
            (new Chat(
                id: $chat->id,
                messages: ChatMessage::collection([
                    new ChatMessage(
                        message: $message,
                        from: AIChatMessageFrom::User,
                        created_at: $createdAt,
                    ),
                ]),
                assistantId: $chat->assistant->assistant_id,
                threadId: null,
            ))->toArray(),
        );

    assertDatabaseHas(AssistantChatMessage::class, [
        'assistant_chat_id' => $chat->getKey(),
        'message' => $message,
        'from' => AIChatMessageFrom::User,
    ]);
});

it('cannot save chats without a name', function () use ($setUp) {
    ['chat' => $chat] = $setUp();

    $chat->delete();

    Livewire::test(PersonalAssistant::class)
        ->set('message', $message = AssistantChatMessage::factory()->make()->message)
        ->call('sendMessage')
        ->callAction('saveChat')
        ->assertHasActionErrors(['name' => 'required']);
});

it('respects message creation time when saving chats', function () use ($setUp) {
    ['user' => $user, 'chat' => $chat] = $setUp();

    $chat->delete();

    // Given that a message was sent at a specific time
    testTime()->freeze();
    $createdAt = now()->toDateTimeString();

    $personalAssistant = Livewire::test(PersonalAssistant::class)
        ->set('message', $message = AssistantChatMessage::factory()->make()->message)
        ->call('sendMessage');

    // And time has elapsed since this message was sent
    testTime()->addMinute();
    testTime()->unfreeze();

    // When the chat is saved
    $personalAssistant
        ->callAction('saveChat', [
            'name' => $name = Str::random(),
        ])
        ->assertHasNoActionErrors();

    assertDatabaseHas(AssistantChat::class, [
        'user_id' => $user->getKey(),
        'name' => $name,
    ]);

    $chat = AssistantChat::query()->latest()->first();

    // Then the message should have been saved with the correct creation time
    assertDatabaseHas(AssistantChatMessage::class, [
        'assistant_chat_id' => $chat->getKey(),
        'message' => $message,
        'from' => AIChatMessageFrom::User,
        'created_at' => $createdAt,
    ]);
})->skip();

it('can select a chat', function () use ($setUp) {
    ['user' => $user, 'chat' => $chat] = $setUp();

    $newChat = AssistantChat::factory()
        ->for($user)
        ->for(AssistantChatFolder::factory()->for($user)->create(), 'folder')
        ->has(AssistantChatMessage::factory()->count(5), 'messages')
        ->create();

    $livewire = Livewire::test(PersonalAssistant::class);

    expect($livewire->chat->toArray())
        ->toEqual(
            (new Chat(
                id: $chat->id,
                messages: ChatMessage::collection($chat->messages),
                assistantId: $chat->assistant->assistant_id,
                threadId: null,
            ))->toArray(),
        );

    $livewire
        ->set('message', AssistantChatMessage::factory()->make()->message)
        ->set('prompt', AssistantChatMessage::factory()->make()->message)
        ->set('renderError', true)
        ->set('error', Str::random())
        ->call('selectChat', $newChat->getKey())
        ->assertSet('message', null)
        ->assertSet('prompt', null)
        ->assertSet('renderError', false)
        ->assertSet('error', null);

    expect($livewire->chat->toArray())
        ->toEqual(
            (new Chat(
                id: $newChat->id,
                messages: ChatMessage::collection($newChat->messages),
                assistantId: $newChat->assistant->assistant_id,
                threadId: null,
            ))->toArray(),
        );
});

it('can not select a chat belonging to a different user', function () use ($setUp) {
    ['chat' => $chat] = $setUp();

    $newChat = AssistantChat::factory()
        ->for($otherUser = User::factory()->create())
        ->for(AssistantChatFolder::factory()->for($otherUser)->create(), 'folder')
        ->has(AssistantChatMessage::factory()->count(5), 'messages')
        ->create();

    $livewire = Livewire::test(PersonalAssistant::class);

    expect($livewire->chat->toArray())
        ->toEqual(
            (new Chat(
                id: $chat->id,
                messages: ChatMessage::collection($chat->messages),
                assistantId: $chat->assistant->assistant_id,
                threadId: null,
            ))->toArray(),
        );

    $livewire
        ->set('message', $message = AssistantChatMessage::factory()->make()->message)
        ->set('prompt', $prompt = AssistantChatMessage::factory()->make()->message)
        ->set('renderError', true)
        ->set('error', $error = Str::random())
        ->call('selectChat', $newChat->getKey())
        ->assertSet('message', $message)
        ->assertSet('prompt', $prompt)
        ->assertSet('renderError', true)
        ->assertSet('error', $error);

    expect($livewire->chat->toArray())
        ->toEqual(
            (new Chat(
                id: $chat->id,
                messages: ChatMessage::collection($chat->messages),
                assistantId: $chat->assistant->assistant_id,
                threadId: null,
            ))->toArray(),
        );
});

it('can start a new chat', function () use ($setUp) {
    ['chat' => $chat] = $setUp();

    $livewire = Livewire::test(PersonalAssistant::class);

    expect($livewire->chat->toArray())
        ->toEqual(
            (new Chat(
                id: $chat->id,
                messages: ChatMessage::collection($chat->messages),
                assistantId: $chat->assistant->assistant_id,
                threadId: null,
            ))->toArray(),
        );

    $livewire
        ->set('message', AssistantChatMessage::factory()->make()->message)
        ->set('prompt', AssistantChatMessage::factory()->make()->message)
        ->set('renderError', true)
        ->set('error', Str::random())
        ->call('newChat')
        ->assertSet('message', null)
        ->assertSet('prompt', null)
        ->assertSet('renderError', false)
        ->assertSet('error', null);

    expect($livewire->chat->toArray())
        ->toEqual(
            (new Chat(
                id: null,
                messages: ChatMessage::collection([]),
                assistantId: $chat->assistant->assistant_id,
                threadId: null,
            ))->toArray(),
        );
});

it('can create a folder', function () use ($setUp) {
    ['user' => $user] = $setUp();

    Livewire::test(PersonalAssistant::class)
        ->callAction('newFolder', [
            'name' => $name = AssistantChatFolder::factory()->make()->name,
        ])
        ->assertHasNoActionErrors();

    assertDatabaseHas(AssistantChatFolder::class, [
        'user_id' => $user->getKey(),
        'name' => $name,
    ]);
});

it('can not create a folder without a name', function () use ($setUp) {
    $setUp();

    Livewire::test(PersonalAssistant::class)
        ->callAction('newFolder', [
            'name' => null,
        ])
        ->assertHasActionErrors(['name' => 'required']);
});

it('can not create a folder with a duplicate name', function () use ($setUp) {
    ['user' => $user] = $setUp();

    $folder = AssistantChatFolder::factory()
        ->for($user)
        ->create();

    Livewire::test(PersonalAssistant::class)
        ->callAction('newFolder', [
            'name' => $folder->name,
        ])
        ->assertHasActionErrors(['name' => 'unique']);
});

it('can create a folder with a duplicate name but belonging to a different user', function () use ($setUp) {
    ['user' => $user] = $setUp();

    $folder = AssistantChatFolder::factory()
        ->for(User::factory()->create())
        ->create();

    Livewire::test(PersonalAssistant::class)
        ->callAction('newFolder', [
            'name' => $folder->name,
        ])
        ->assertHasNoActionErrors();

    assertDatabaseHas(AssistantChatFolder::class, [
        'user_id' => $user->getKey(),
        'name' => $folder->name,
    ]);
});

it('can rename a folder', function () use ($setUp) {
    ['user' => $user] = $setUp();

    $folder = AssistantChatFolder::factory()
        ->for($user)
        ->create();

    Livewire::test(PersonalAssistant::class)
        ->callAction('renameFolder', [
            'name' => $name = AssistantChatFolder::factory()->make()->name,
        ], arguments: [
            'folder' => $folder->getKey(),
        ])
        ->assertHasNoActionErrors();

    assertDatabaseHas(AssistantChatFolder::class, [
        'id' => $folder->getKey(),
        'name' => $name,
    ]);
});

it('can not rename a folder without a name', function () use ($setUp) {
    ['user' => $user] = $setUp();

    $folder = AssistantChatFolder::factory()
        ->for($user)
        ->create();

    Livewire::test(PersonalAssistant::class)
        ->callAction('renameFolder', [
            'name' => null,
        ], arguments: [
            'folder' => $folder->getKey(),
        ])
        ->assertHasActionErrors(['name' => 'required']);
});

it('can not rename a folder with a duplicate name', function () use ($setUp) {
    ['user' => $user] = $setUp();

    $folder = AssistantChatFolder::factory()
        ->for($user)
        ->create();

    $otherFolder = AssistantChatFolder::factory()
        ->for($user)
        ->create();

    Livewire::test(PersonalAssistant::class)
        ->callAction('renameFolder', [
            'name' => $otherFolder->name,
        ], arguments: [
            'folder' => $folder->getKey(),
        ])
        ->assertHasActionErrors(['name' => 'unique']);
});

it('can rename a folder with a duplicate name but belonging to a different user', function () use ($setUp) {
    ['user' => $user] = $setUp();

    $folder = AssistantChatFolder::factory()
        ->for($user)
        ->create();

    $otherFolder = AssistantChatFolder::factory()
        ->for(User::factory()->create())
        ->create();

    Livewire::test(PersonalAssistant::class)
        ->callAction('renameFolder', [
            'name' => $otherFolder->name,
        ], arguments: [
            'folder' => $folder->getKey(),
        ])
        ->assertHasNoActionErrors();

    assertDatabaseHas(AssistantChatFolder::class, [
        'id' => $folder->getKey(),
        'name' => $otherFolder->name,
    ]);
});

it('can not rename a folder belonging to a different user', function () use ($setUp) {
    $setUp();

    $folder = AssistantChatFolder::factory()
        ->for(User::factory()->create())
        ->create();

    $oldFolderName = $folder->name;

    Livewire::test(PersonalAssistant::class)
        ->callAction('renameFolder', [
            'name' => $newFolderName = AssistantChatFolder::factory()->make()->name,
        ], arguments: [
            'folder' => $folder->getKey(),
        ]);

    assertDatabaseHas(AssistantChatFolder::class, [
        'id' => $folder->getKey(),
        'name' => $oldFolderName,
    ]);

    expect($oldFolderName)
        ->not->toEqual($newFolderName);
});

it('can delete a folder', function () use ($setUp) {
    ['user' => $user] = $setUp();

    $folder = AssistantChatFolder::factory()
        ->for($user)
        ->create();

    Livewire::test(PersonalAssistant::class)
        ->callAction('deleteFolder', arguments: [
            'folder' => $folder->getKey(),
        ]);

    assertSoftDeleted(AssistantChatFolder::class, [
        'id' => $folder->getKey(),
    ]);
});

it('can not delete a folder belonging to a different user', function () use ($setUp) {
    $setUp();

    $folder = AssistantChatFolder::factory()
        ->for(User::factory()->create())
        ->create();

    Livewire::test(PersonalAssistant::class)
        ->callAction('deleteFolder', arguments: [
            'folder' => $folder->getKey(),
        ]);

    assertNotSoftDeleted(AssistantChatFolder::class, [
        'id' => $folder->getKey(),
    ]);
});

it('can move a chat in to a folder', function () use ($setUp) {
    ['user' => $user, 'chat' => $chat] = $setUp();

    $folder = AssistantChatFolder::factory()
        ->for($user)
        ->create();

    Livewire::test(PersonalAssistant::class)
        ->callAction('moveChat', [
            'folder' => $folder->getKey(),
        ], arguments: [
            'chat' => $chat->getKey(),
        ]);

    assertDatabaseHas(AssistantChat::class, [
        'id' => $chat->getKey(),
        'assistant_chat_folder_id' => $folder->getKey(),
    ]);
});

it('can move a chat between folders', function () use ($setUp) {
    ['user' => $user, 'chat' => $chat] = $setUp();

    $folder = AssistantChatFolder::factory()
        ->for($user)
        ->create();

    $chat->folder()->associate($folder);
    $chat->save();

    $newFolder = AssistantChatFolder::factory()
        ->for($user)
        ->create();

    Livewire::test(PersonalAssistant::class)
        ->callAction('moveChat', [
            'folder' => $newFolder->getKey(),
        ], arguments: [
            'chat' => $chat->getKey(),
        ]);

    assertDatabaseHas(AssistantChat::class, [
        'id' => $chat->getKey(),
        'assistant_chat_folder_id' => $newFolder->getKey(),
    ]);
});

it('can move a chat out of a folder', function () use ($setUp) {
    ['user' => $user, 'chat' => $chat] = $setUp();

    $folder = AssistantChatFolder::factory()
        ->for($user)
        ->create();

    $chat->folder()->associate($folder);
    $chat->save();

    Livewire::test(PersonalAssistant::class)
        ->callAction('moveChat', [
            'folder' => null,
        ], arguments: [
            'chat' => $chat->getKey(),
        ]);

    assertDatabaseHas(AssistantChat::class, [
        'id' => $chat->getKey(),
        'assistant_chat_folder_id' => null,
    ]);
});

it('can not move a chat belonging to a different user in to a folder', function () use ($setUp) {
    ['user' => $user, 'chat' => $chat] = $setUp();

    $chat->user()->associate(User::factory()->create());
    $chat->save();

    $folder = AssistantChatFolder::factory()
        ->for($user)
        ->create();

    Livewire::test(PersonalAssistant::class)
        ->callAction('moveChat', [
            'folder' => $folder->getKey(),
        ], arguments: [
            'chat' => $chat->getKey(),
        ]);

    assertDatabaseHas(AssistantChat::class, [
        'id' => $chat->getKey(),
        'assistant_chat_folder_id' => null,
    ]);
});

it('can not move a chat in to a folder belonging to a different user', function () use ($setUp) {
    ['chat' => $chat] = $setUp();

    $folder = AssistantChatFolder::factory()
        ->for(User::factory()->create())
        ->create();

    Livewire::test(PersonalAssistant::class)
        ->callAction('moveChat', [
            'folder' => $folder->getKey(),
        ], arguments: [
            'chat' => $chat->getKey(),
        ]);

    assertDatabaseHas(AssistantChat::class, [
        'id' => $chat->getKey(),
        'assistant_chat_folder_id' => null,
    ]);
});

it('can move a chat in to a folder with drag and drop', function () use ($setUp) {
    ['user' => $user, 'chat' => $chat] = $setUp();

    $folder = AssistantChatFolder::factory()
        ->for($user)
        ->create();

    Livewire::test(PersonalAssistant::class)
        ->call('movedChat', $chat->getKey(), $folder->getKey())
        ->assertOk();

    assertDatabaseHas(AssistantChat::class, [
        'id' => $chat->getKey(),
        'assistant_chat_folder_id' => $folder->getKey(),
    ]);
});

it('can move a chat between folders with drag and drop', function () use ($setUp) {
    ['user' => $user, 'chat' => $chat] = $setUp();

    $folder = AssistantChatFolder::factory()
        ->for($user)
        ->create();

    $chat->folder()->associate($folder);
    $chat->save();

    $newFolder = AssistantChatFolder::factory()
        ->for($user)
        ->create();

    Livewire::test(PersonalAssistant::class)
        ->call('movedChat', $chat->getKey(), $newFolder->getKey())
        ->assertOk();

    assertDatabaseHas(AssistantChat::class, [
        'id' => $chat->getKey(),
        'assistant_chat_folder_id' => $newFolder->getKey(),
    ]);
});

it('can move a chat out of a folder with drag and drop', function () use ($setUp) {
    ['user' => $user, 'chat' => $chat] = $setUp();

    $folder = AssistantChatFolder::factory()
        ->for($user)
        ->create();

    $chat->folder()->associate($folder);
    $chat->save();

    Livewire::test(PersonalAssistant::class)
        ->call('movedChat', $chat->getKey(), null)
        ->assertOk();

    assertDatabaseHas(AssistantChat::class, [
        'id' => $chat->getKey(),
        'assistant_chat_folder_id' => null,
    ]);
});

it('can not move a chat belonging to a different user in to a folder with drag and drop', function () use ($setUp) {
    ['user' => $user, 'chat' => $chat] = $setUp();

    $chat->user()->associate(User::factory()->create());
    $chat->save();

    $folder = AssistantChatFolder::factory()
        ->for($user)
        ->create();

    Livewire::test(PersonalAssistant::class)
        ->call('movedChat', $chat->getKey(), $folder->getKey())
        ->assertOk();

    assertDatabaseHas(AssistantChat::class, [
        'id' => $chat->getKey(),
        'assistant_chat_folder_id' => null,
    ]);
});

it('can not move a chat in to a folder belonging to a different user with drag and drop', function () use ($setUp) {
    ['chat' => $chat] = $setUp();

    $folder = AssistantChatFolder::factory()
        ->for(User::factory()->create())
        ->create();

    Livewire::test(PersonalAssistant::class)
        ->call('movedChat', $chat->getKey(), $folder->getKey())
        ->assertOk();

    assertDatabaseHas(AssistantChat::class, [
        'id' => $chat->getKey(),
        'assistant_chat_folder_id' => null,
    ]);
});

it('can delete a chat', function () use ($setUp) {
    ['chat' => $chat] = $setUp();

    $livewire = Livewire::test(PersonalAssistant::class)
        ->callAction('deleteChat', arguments: [
            'chat' => $chat->getKey(),
        ]);

    expect($livewire->chat->toArray())
        ->toEqual(
            (new Chat(
                id: null,
                messages: ChatMessage::collection([]),
                assistantId: $chat->assistant->assistant_id,
                threadId: null,
            ))->toArray(),
        );

    assertSoftDeleted(AssistantChat::class, [
        'id' => $chat->getKey(),
    ]);
});

it('can not delete a chat belonging to a different user', function () use ($setUp) {
    $setUp();

    $chatBelongingToAnotherUser = AssistantChat::factory()
        ->for(User::factory()->create())
        ->create();

    Livewire::test(PersonalAssistant::class)
        ->callAction('deleteChat', arguments: [
            'chat' => $chatBelongingToAnotherUser->getKey(),
        ]);

    assertNotSoftDeleted(AssistantChat::class, [
        'id' => $chatBelongingToAnotherUser->getKey(),
    ]);
});

it('can insert a prompt from the library', function () use ($setUp) {
    ['user' => $user] = $setUp();

    $prompt = Prompt::factory()->create();

    assertDatabaseMissing(PromptUse::class, [
        'prompt_id' => $prompt->getKey(),
        'user_id' => $user->getKey(),
    ]);

    Livewire::test(PersonalAssistant::class)
        ->callAction('insertFromPromptLibrary', [
            'promptId' => $prompt->getKey(),
        ])
        ->assertHasNoActionErrors()
        ->assertSet('message', $prompt->prompt);

    assertDatabaseHas(PromptUse::class, [
        'prompt_id' => $prompt->getKey(),
        'user_id' => $user->getKey(),
    ]);
});

it('can not insert a missing prompt from the library', function () use ($setUp) {
    $setUp();

    Livewire::test(PersonalAssistant::class)
        ->callAction('insertFromPromptLibrary', [
            'promptId' => null,
        ])
        ->assertHasActionErrors(['promptId' => 'required']);
});

it('can upvote a prompt from the library while inserting it', function () use ($setUp) {
    ['user' => $user] = $setUp();

    $prompt = Prompt::factory()->create();

    assertDatabaseMissing(PromptUpvote::class, [
        'prompt_id' => $prompt->getKey(),
        'user_id' => $user->getKey(),
    ]);

    Livewire::test(PersonalAssistant::class)
        ->mountAction('insertFromPromptLibrary')
        ->setActionData([
            'promptId' => $prompt->getKey(),
        ])
        ->callFormComponentAction('promptId', 'upvote', formName: 'mountedActionForm');

    assertDatabaseHas(PromptUpvote::class, [
        'prompt_id' => $prompt->getKey(),
        'user_id' => $user->getKey(),
    ]);
});

it('can remove upvote from a prompt from the library while inserting it', function () use ($setUp) {
    ['user' => $user] = $setUp();

    $prompt = Prompt::factory()->create();
    $prompt->toggleUpvote();

    assertDatabaseHas(PromptUpvote::class, [
        'prompt_id' => $prompt->getKey(),
        'user_id' => $user->getKey(),
    ]);

    Livewire::test(PersonalAssistant::class)
        ->mountAction('insertFromPromptLibrary')
        ->setActionData([
            'promptId' => $prompt->getKey(),
        ])
        ->callFormComponentAction('promptId', 'upvote', formName: 'mountedActionForm');

    assertSoftDeleted(PromptUpvote::class, [
        'prompt_id' => $prompt->getKey(),
        'user_id' => $user->getKey(),
    ]);
});

it('can rename a chat', function () use ($setUp) {
    ['chat' => $chat] = $setUp();

    Livewire::test(PersonalAssistant::class)
        ->callAction('editChat', [
            'name' => $name = AssistantChat::factory()->make()->name,
        ], arguments: [
            'chat' => $chat->getKey(),
        ])
        ->assertHasNoActionErrors();

    assertDatabaseHas(AssistantChat::class, [
        'id' => $chat->getKey(),
        'name' => $name,
    ]);
});

it('can not rename a chat without a name', function () use ($setUp) {
    ['chat' => $chat] = $setUp();

    Livewire::test(PersonalAssistant::class)
        ->callAction('editChat', [
            'name' => null,
        ], arguments: [
            'chat' => $chat->getKey(),
        ])
        ->assertHasActionErrors(['name' => 'required']);
});

it('can not rename a chat belonging to a different user', function () use ($setUp) {
    ['chat' => $chat] = $setUp();

    $chat->user()->associate(User::factory()->create());
    $chat->save();

    $oldChatName = $chat->name;

    Livewire::test(PersonalAssistant::class)
        ->callAction('editChat', [
            'name' => $newChatName = AssistantChat::factory()->make()->name,
        ], arguments: [
            'chat' => $chat->getKey(),
        ])
        ->assertHasNoActionErrors();

    assertDatabaseHas(AssistantChat::class, [
        'id' => $chat->getKey(),
        'name' => $oldChatName,
    ]);

    expect($oldChatName)
        ->not->toEqual($newChatName);
});

it('can clone a chat to a user', function () use ($setUp) {
    Bus::fake();

    ['user' => $user, 'chat' => $chat] = $setUp();

    $otherUser = User::factory()->create();

    Livewire::test(PersonalAssistant::class)
        ->callAction('cloneChat', [
            'target_type' => AssistantChatShareWith::User->value,
            'target_ids' => [$otherUser->getKey()],
        ], arguments: [
            'chat' => $chat->getKey(),
        ])
        ->assertHasNoActionErrors();

    Bus::assertDispatched(ShareAssistantChatsJob::class, function (ShareAssistantChatsJob $job) use ($chat, $otherUser, $user) {
        if (! $job->chat->is($chat)) {
            return false;
        }

        if ($job->via !== AssistantChatShareVia::Internal) {
            return false;
        }

        if ($job->targetType !== AssistantChatShareWith::User) {
            return false;
        }

        if ($job->targetIds !== [$otherUser->getKey()]) {
            return false;
        }

        if (! $job->sender->is($user)) {
            return false;
        }

        return true;
    });
});

it('can clone a chat to a team', function () use ($setUp) {
    Bus::fake();

    ['user' => $user, 'chat' => $chat] = $setUp();

    $team = Team::factory()->create();

    Livewire::test(PersonalAssistant::class)
        ->callAction('cloneChat', [
            'target_type' => AssistantChatShareWith::Team->value,
            'target_ids' => [$team->getKey()],
        ], arguments: [
            'chat' => $chat->getKey(),
        ])
        ->assertHasNoActionErrors();

    Bus::assertDispatched(ShareAssistantChatsJob::class, function (ShareAssistantChatsJob $job) use ($chat, $team, $user) {
        if (! $job->chat->is($chat)) {
            return false;
        }

        if ($job->via !== AssistantChatShareVia::Internal) {
            return false;
        }

        if ($job->targetType !== AssistantChatShareWith::Team) {
            return false;
        }

        if ($job->targetIds !== [$team->getKey()]) {
            return false;
        }

        if (! $job->sender->is($user)) {
            return false;
        }

        return true;
    });
});

it('can not clone a chat without a target type', function () use ($setUp) {
    ['chat' => $chat] = $setUp();

    $otherUser = User::factory()->create();

    Livewire::test(PersonalAssistant::class)
        ->callAction('cloneChat', [
            'target_type' => null,
            'target_ids' => [$otherUser->getKey()],
        ], arguments: [
            'chat' => $chat->getKey(),
        ])
        ->assertHasActionErrors(['target_type' => 'required']);
});

it('can not clone a chat without any targets', function () use ($setUp) {
    ['chat' => $chat] = $setUp();

    Livewire::test(PersonalAssistant::class)
        ->callAction('cloneChat', [
            'target_type' => AssistantChatShareWith::User->value,
            'target_ids' => [],
        ], arguments: [
            'chat' => $chat->getKey(),
        ])
        ->assertHasActionErrors(['target_ids' => 'required']);
});

it('can not clone a chat belonging to a different user', function () use ($setUp) {
    Bus::fake();

    ['chat' => $chat] = $setUp();

    $chat->user()->associate(User::factory()->create());
    $chat->save();

    $otherUser = User::factory()->create();

    Livewire::test(PersonalAssistant::class)
        ->callAction('cloneChat', [
            'target_type' => AssistantChatShareWith::User->value,
            'target_ids' => [$otherUser->getKey()],
        ], arguments: [
            'chat' => $chat->getKey(),
        ]);

    Bus::assertNotDispatched(ShareAssistantChatsJob::class);
});

it('can email a chat to a user', function () use ($setUp) {
    Bus::fake();

    ['user' => $user, 'chat' => $chat] = $setUp();

    $otherUser = User::factory()->create();

    Livewire::test(PersonalAssistant::class)
        ->callAction('emailChat', [
            'target_type' => AssistantChatShareWith::User->value,
            'target_ids' => [$otherUser->getKey()],
        ], arguments: [
            'chat' => $chat->getKey(),
        ])
        ->assertHasNoActionErrors();

    Bus::assertDispatched(ShareAssistantChatsJob::class, function (ShareAssistantChatsJob $job) use ($chat, $otherUser, $user) {
        if (! $job->chat->is($chat)) {
            return false;
        }

        if ($job->via !== AssistantChatShareVia::Email) {
            return false;
        }

        if ($job->targetType !== AssistantChatShareWith::User) {
            return false;
        }

        if ($job->targetIds !== [$otherUser->getKey()]) {
            return false;
        }

        if (! $job->sender->is($user)) {
            return false;
        }

        return true;
    });
});

it('can email a chat to a team', function () use ($setUp) {
    Bus::fake();

    ['user' => $user, 'chat' => $chat] = $setUp();

    $team = Team::factory()->create();

    Livewire::test(PersonalAssistant::class)
        ->callAction('emailChat', [
            'target_type' => AssistantChatShareWith::Team->value,
            'target_ids' => [$team->getKey()],
        ], arguments: [
            'chat' => $chat->getKey(),
        ])
        ->assertHasNoActionErrors();

    Bus::assertDispatched(ShareAssistantChatsJob::class, function (ShareAssistantChatsJob $job) use ($chat, $team, $user) {
        if (! $job->chat->is($chat)) {
            return false;
        }

        if ($job->via !== AssistantChatShareVia::Email) {
            return false;
        }

        if ($job->targetType !== AssistantChatShareWith::Team) {
            return false;
        }

        if ($job->targetIds !== [$team->getKey()]) {
            return false;
        }

        if (! $job->sender->is($user)) {
            return false;
        }

        return true;
    });
});

it('can not email a chat without a target type', function () use ($setUp) {
    ['chat' => $chat] = $setUp();

    $otherUser = User::factory()->create();

    Livewire::test(PersonalAssistant::class)
        ->callAction('emailChat', [
            'target_type' => null,
            'target_ids' => [$otherUser->getKey()],
        ], arguments: [
            'chat' => $chat->getKey(),
        ])
        ->assertHasActionErrors(['target_type' => 'required']);
});

it('can not email a chat without any targets', function () use ($setUp) {
    ['chat' => $chat] = $setUp();

    Livewire::test(PersonalAssistant::class)
        ->callAction('emailChat', [
            'target_type' => AssistantChatShareWith::User->value,
            'target_ids' => [],
        ], arguments: [
            'chat' => $chat->getKey(),
        ])
        ->assertHasActionErrors(['target_ids' => 'required']);
});

it('can not email a chat belonging to a different user', function () use ($setUp) {
    Bus::fake();

    ['chat' => $chat] = $setUp();

    $chat->user()->associate(User::factory()->create());
    $chat->save();

    $otherUser = User::factory()->create();

    Livewire::test(PersonalAssistant::class)
        ->callAction('emailChat', [
            'target_type' => AssistantChatShareWith::User->value,
            'target_ids' => [$otherUser->getKey()],
        ], arguments: [
            'chat' => $chat->getKey(),
        ]);

    Bus::assertNotDispatched(ShareAssistantChatsJob::class);
});
