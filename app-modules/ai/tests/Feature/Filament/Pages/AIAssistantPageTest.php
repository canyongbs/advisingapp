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

use AdvisingApp\Ai\Enums\AiModel;
use AdvisingApp\Ai\Models\Prompt;
use AdvisingApp\Team\Models\Team;
use App\Filament\Pages\Dashboard;
use AdvisingApp\Ai\Models\AiThread;
use Illuminate\Support\Facades\Bus;
use AdvisingApp\Ai\Models\AiMessage;
use AdvisingApp\Ai\Models\PromptUse;
use AdvisingApp\Ai\Models\AiAssistant;
use AdvisingApp\Ai\Enums\AiApplication;
use AdvisingApp\Ai\Models\PromptUpvote;
use AdvisingApp\Ai\Models\AiThreadFolder;
use AdvisingApp\Ai\Enums\AiThreadShareTarget;
use AdvisingApp\Ai\Jobs\PrepareAiThreadCloning;
use AdvisingApp\Ai\Jobs\PrepareAiThreadEmailing;
use AdvisingApp\Authorization\Enums\LicenseType;
use AdvisingApp\Consent\Models\ConsentAgreement;
use AdvisingApp\Consent\Enums\ConsentAgreementType;
use AdvisingApp\Assistant\Filament\Pages\PersonalAssistant;

use function Pest\Laravel\{actingAs,
    assertDatabaseHas,
    assertDatabaseMissing,
    assertNotSoftDeleted,
    assertSoftDeleted
};

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

    $assistant = AiAssistant::factory()->create([
        'application' => AiApplication::PersonalAssistant,
        'is_default' => true,
        'model' => AiModel::Test,
    ]);

    $thread = AiThread::factory()
        ->for($assistant, 'assistant')
        ->for($user)
        ->has(AiMessage::factory()->count(5), 'messages')
        ->create();

    return ['user' => $user, 'assistant' => $assistant, 'consentAgreement' => $consentAgreement, 'thread' => $thread];
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
        ->assertSet('isConsented', false)
        ->assertSee($consentAgreement->title)
        ->assertSeeHtml(str($consentAgreement->description)->markdown()->sanitizeHtml()->toHtmlString())
        ->assertSeeHtml(str($consentAgreement->body)->markdown()->sanitizeHtml()->toHtmlString());
});

it('will show the AI Assistant interface if the user has agreed to the terms and conditions of use', function () use ($setUp) {
    ['consentAgreement' => $consentAgreement] = $setUp();

    Livewire::test(PersonalAssistant::class)
        ->assertSet('isConsented', true)
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
        ->assertSet('isConsented', false)
        ->assertSee($consentAgreement->title)
        ->assertSeeHtml(str($consentAgreement->description)->markdown()->sanitizeHtml()->toHtmlString())
        ->assertSeeHtml(str($consentAgreement->body)->markdown()->sanitizeHtml()->toHtmlString());

    $livewire
        ->call('confirmConsent')
        ->assertDontSee($consentAgreement->title)
        ->assertDontSee($consentAgreement->description)
        ->assertDontSee($consentAgreement->body);

    expect($user->hasConsentedTo($consentAgreement))->toBeTrue();
});

it('will automatically set the current thread when it does not have a folder', function () use ($setUp) {
    ['thread' => $thread] = $setUp();

    Livewire::test(PersonalAssistant::class)
        ->call('loadFirstThread')
        ->assertSet('thread.id', $thread->id);
});

it('will automatically set the current thread to the most recently updated one without a folder', function () use ($setUp) {
    ['user' => $user, 'assistant' => $assistant] = $setUp();

    $newerThread = AiThread::factory()
        ->for($assistant, 'assistant')
        ->for($user)
        ->has(AiMessage::factory()->count(5), 'messages')
        ->create([
            'updated_at' => now()->addMinute(),
        ]);

    Livewire::test(PersonalAssistant::class)
        ->call('loadFirstThread')
        ->assertSet('thread.id', $newerThread->id);
});

it('will not automatically set the current thread to one with a folder', function () use ($setUp) {
    ['user' => $user, 'thread' => $thread] = $setUp();

    $thread->folder()->associate(AiThreadFolder::factory()->for($user)->create([
        'application' => AiApplication::PersonalAssistant,
    ]));
    $thread->save();

    Livewire::test(PersonalAssistant::class)
        ->call('loadFirstThread')
        ->assertNotSet('thread.id', $thread->id);
});

it('will not automatically set the current thread to one belonging to another user', function () use ($setUp) {
    ['thread' => $thread] = $setUp();

    $thread->user()->associate(User::factory()->create());
    $thread->save();

    Livewire::test(PersonalAssistant::class)
        ->call('loadFirstThread')
        ->assertNotSet('thread.id', $thread->id);
});

it('can save threads', function () use ($setUp) {
    ['user' => $user, 'assistant' => $assistant] = $setUp();

    $thread = AiThread::factory()
        ->for($assistant, 'assistant')
        ->for($user)
        ->create([
            'name' => null,
        ]);

    Livewire::test(PersonalAssistant::class)
        ->call('selectThread', $thread)
        ->callAction('saveThread', [
            'name' => $name = Str::random(),
        ])
        ->assertHasNoActionErrors();

    assertDatabaseHas(AiThread::class, [
        'id' => $thread->getKey(),
        'user_id' => $user->getKey(),
        'name' => $name,
    ]);
});

it('can save threads into a folder', function () use ($setUp) {
    ['user' => $user, 'assistant' => $assistant] = $setUp();

    $thread = AiThread::factory()
        ->for($assistant, 'assistant')
        ->for($user)
        ->create([
            'name' => null,
        ]);

    $folder = AiThreadFolder::factory()
        ->for($user)
        ->create([
            'application' => AiApplication::PersonalAssistant,
        ]);

    Livewire::test(PersonalAssistant::class)
        ->call('selectThread', $thread)
        ->callAction('saveThread', [
            'name' => $name = Str::random(),
            'folder' => $folder->getKey(),
        ])
        ->assertHasNoActionErrors();

    assertDatabaseHas(AiThread::class, [
        'id' => $thread->getKey(),
        'name' => $name,
        'folder_id' => $folder->getKey(),
    ]);
});

it('cannot save threads without a name', function () use ($setUp) {
    ['user' => $user, 'assistant' => $assistant] = $setUp();

    AiThread::factory()
        ->for($assistant, 'assistant')
        ->for($user)
        ->create([
            'name' => null,
        ]);

    Livewire::test(PersonalAssistant::class)
        ->call('loadFirstThread')
        ->callAction('saveThread')
        ->assertHasActionErrors(['name' => 'required']);
});

it('can select a thread', function () use ($setUp) {
    ['user' => $user, 'assistant' => $assistant, 'thread' => $thread] = $setUp();

    $newThread = AiThread::factory()
        ->for($assistant, 'assistant')
        ->for($user)
        ->for(AiThreadFolder::factory()->for($user)->create([
            'application' => AiApplication::PersonalAssistant,
        ]), 'folder')
        ->has(AiMessage::factory()->count(5), 'messages')
        ->create();

    Livewire::test(PersonalAssistant::class)
        ->call('loadFirstThread')
        ->assertSet('thread.id', $thread->id)
        ->call('selectThread', $newThread)
        ->assertSet('thread.id', $newThread->id);
});

it('can not select a thread belonging to a different user', function () use ($setUp) {
    ['assistant' => $assistant, 'thread' => $thread] = $setUp();

    $newThread = AiThread::factory()
        ->for($assistant, 'assistant')
        ->for($otherUser = User::factory()->create())
        ->for(AiThreadFolder::factory()->for($otherUser)->create([
            'application' => AiApplication::PersonalAssistant,
        ]), 'folder')
        ->has(AiMessage::factory()->count(5), 'messages')
        ->create();

    Livewire::test(PersonalAssistant::class)
        ->call('loadFirstThread')
        ->assertSet('thread.id', $thread->id)
        ->call('selectThread', $newThread)
        ->assertNotFound();
});

it('can start a new thread', function () use ($setUp) {
    ['thread' => $thread] = $setUp();

    Livewire::test(PersonalAssistant::class)
        ->call('loadFirstThread')
        ->assertSet('thread.id', $thread->id)
        ->call('createThread')
        ->assertNotSet('thread.id', $thread->id);
});

it('can create a folder', function () use ($setUp) {
    ['user' => $user] = $setUp();

    Livewire::test(PersonalAssistant::class)
        ->callAction('newFolder', [
            'name' => $name = AiThreadFolder::factory()->make()->name,
        ])
        ->assertHasNoActionErrors();

    assertDatabaseHas(AiThreadFolder::class, [
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

    $folder = AiThreadFolder::factory()
        ->for($user)
        ->create([
            'application' => AiApplication::PersonalAssistant,
        ]);

    Livewire::test(PersonalAssistant::class)
        ->callAction('newFolder', [
            'name' => $folder->name,
        ])
        ->assertHasActionErrors(['name' => 'unique']);
});

it('can create a folder with a duplicate name but belonging to a different user', function () use ($setUp) {
    ['user' => $user] = $setUp();

    $folder = AiThreadFolder::factory()
        ->for(User::factory()->create())
        ->create([
            'application' => AiApplication::PersonalAssistant,
        ]);

    Livewire::test(PersonalAssistant::class)
        ->callAction('newFolder', [
            'name' => $folder->name,
        ])
        ->assertHasNoActionErrors();

    assertDatabaseHas(AiThreadFolder::class, [
        'user_id' => $user->getKey(),
        'name' => $folder->name,
    ]);
});

it('can rename a folder', function () use ($setUp) {
    ['user' => $user] = $setUp();

    $folder = AiThreadFolder::factory()
        ->for($user)
        ->create([
            'application' => AiApplication::PersonalAssistant,
        ]);

    Livewire::test(PersonalAssistant::class)
        ->callAction('renameFolder', [
            'name' => $name = AiThreadFolder::factory()->make()->name,
        ], arguments: [
            'folder' => $folder->getKey(),
        ])
        ->assertHasNoActionErrors();

    assertDatabaseHas(AiThreadFolder::class, [
        'id' => $folder->getKey(),
        'name' => $name,
    ]);
});

it('can not rename a folder without a name', function () use ($setUp) {
    ['user' => $user] = $setUp();

    $folder = AiThreadFolder::factory()
        ->for($user)
        ->create([
            'application' => AiApplication::PersonalAssistant,
        ]);

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

    $folder = AiThreadFolder::factory()
        ->for($user)
        ->create([
            'application' => AiApplication::PersonalAssistant,
        ]);

    $otherFolder = AiThreadFolder::factory()
        ->for($user)
        ->create([
            'application' => AiApplication::PersonalAssistant,
        ]);

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

    $folder = AiThreadFolder::factory()
        ->for($user)
        ->create([
            'application' => AiApplication::PersonalAssistant,
        ]);

    $otherFolder = AiThreadFolder::factory()
        ->for(User::factory()->create())
        ->create([
            'application' => AiApplication::PersonalAssistant,
        ]);

    Livewire::test(PersonalAssistant::class)
        ->callAction('renameFolder', [
            'name' => $otherFolder->name,
        ], arguments: [
            'folder' => $folder->getKey(),
        ])
        ->assertHasNoActionErrors();

    assertDatabaseHas(AiThreadFolder::class, [
        'id' => $folder->getKey(),
        'name' => $otherFolder->name,
    ]);
});

it('can not rename a folder belonging to a different user', function () use ($setUp) {
    $setUp();

    $folder = AiThreadFolder::factory()
        ->for(User::factory()->create())
        ->create([
            'application' => AiApplication::PersonalAssistant,
        ]);

    $oldFolderName = $folder->name;

    Livewire::test(PersonalAssistant::class)
        ->callAction('renameFolder', [
            'name' => $newFolderName = AiThreadFolder::factory()->make()->name,
        ], arguments: [
            'folder' => $folder->getKey(),
        ]);

    assertDatabaseHas(AiThreadFolder::class, [
        'id' => $folder->getKey(),
        'name' => $oldFolderName,
    ]);

    expect($oldFolderName)
        ->not->toEqual($newFolderName);
});

it('can delete a folder', function () use ($setUp) {
    ['user' => $user] = $setUp();

    $folder = AiThreadFolder::factory()
        ->for($user)
        ->create([
            'application' => AiApplication::PersonalAssistant,
        ]);

    Livewire::test(PersonalAssistant::class)
        ->callAction('deleteFolder', arguments: [
            'folder' => $folder->getKey(),
        ]);

    assertSoftDeleted(AiThreadFolder::class, [
        'id' => $folder->getKey(),
    ]);
});

it('can not delete a folder belonging to a different user', function () use ($setUp) {
    $setUp();

    $folder = AiThreadFolder::factory()
        ->for(User::factory()->create())
        ->create([
            'application' => AiApplication::PersonalAssistant,
        ]);

    Livewire::test(PersonalAssistant::class)
        ->callAction('deleteFolder', arguments: [
            'folder' => $folder->getKey(),
        ]);

    assertNotSoftDeleted(AiThreadFolder::class, [
        'id' => $folder->getKey(),
    ]);
});

it('can move a thread in to a folder', function () use ($setUp) {
    ['user' => $user, 'thread' => $thread] = $setUp();

    $folder = AiThreadFolder::factory()
        ->for($user)
        ->create([
            'application' => AiApplication::PersonalAssistant,
        ]);

    Livewire::test(PersonalAssistant::class)
        ->callAction('moveThread', [
            'folder' => $folder->getKey(),
        ], arguments: [
            'thread' => $thread->getKey(),
        ]);

    assertDatabaseHas(AiThread::class, [
        'id' => $thread->getKey(),
        'folder_id' => $folder->getKey(),
    ]);
});

it('can move a thread between folders', function () use ($setUp) {
    ['user' => $user, 'thread' => $thread] = $setUp();

    $folder = AiThreadFolder::factory()
        ->for($user)
        ->create([
            'application' => AiApplication::PersonalAssistant,
        ]);

    $thread->folder()->associate($folder);
    $thread->save();

    $newFolder = AiThreadFolder::factory()
        ->for($user)
        ->create([
            'application' => AiApplication::PersonalAssistant,
        ]);

    Livewire::test(PersonalAssistant::class)
        ->callAction('moveThread', [
            'folder' => $newFolder->getKey(),
        ], arguments: [
            'thread' => $thread->getKey(),
        ]);

    assertDatabaseHas(AiThread::class, [
        'id' => $thread->getKey(),
        'folder_id' => $newFolder->getKey(),
    ]);
});

it('can move a thread out of a folder', function () use ($setUp) {
    ['user' => $user, 'thread' => $thread] = $setUp();

    $folder = AiThreadFolder::factory()
        ->for($user)
        ->create([
            'application' => AiApplication::PersonalAssistant,
        ]);

    $thread->folder()->associate($folder);
    $thread->save();

    Livewire::test(PersonalAssistant::class)
        ->callAction('moveThread', [
            'folder' => null,
        ], arguments: [
            'thread' => $thread->getKey(),
        ]);

    assertDatabaseHas(AiThread::class, [
        'id' => $thread->getKey(),
        'folder_id' => null,
    ]);
});

it('can not move a thread belonging to a different user in to a folder', function () use ($setUp) {
    ['user' => $user, 'thread' => $thread] = $setUp();

    $thread->user()->associate(User::factory()->create());
    $thread->save();

    $folder = AiThreadFolder::factory()
        ->for($user)
        ->create([
            'application' => AiApplication::PersonalAssistant,
        ]);

    Livewire::test(PersonalAssistant::class)
        ->callAction('moveThread', [
            'folder' => $folder->getKey(),
        ], arguments: [
            'thread' => $thread->getKey(),
        ]);

    assertDatabaseHas(AiThread::class, [
        'id' => $thread->getKey(),
        'folder_id' => null,
    ]);
});

it('can not move a thread in to a folder belonging to a different user', function () use ($setUp) {
    ['thread' => $thread] = $setUp();

    $folder = AiThreadFolder::factory()
        ->for(User::factory()->create())
        ->create([
            'application' => AiApplication::PersonalAssistant,
        ]);

    Livewire::test(PersonalAssistant::class)
        ->callAction('moveThread', [
            'folder' => $folder->getKey(),
        ], arguments: [
            'thread' => $thread->getKey(),
        ]);

    assertDatabaseHas(AiThread::class, [
        'id' => $thread->getKey(),
        'folder_id' => null,
    ]);
});

it('can move a thread in to a folder with drag and drop', function () use ($setUp) {
    ['user' => $user, 'thread' => $thread] = $setUp();

    $folder = AiThreadFolder::factory()
        ->for($user)
        ->create([
            'application' => AiApplication::PersonalAssistant,
        ]);

    Livewire::test(PersonalAssistant::class)
        ->call('movedThread', $thread->getKey(), $folder->getKey())
        ->assertOk();

    assertDatabaseHas(AiThread::class, [
        'id' => $thread->getKey(),
        'folder_id' => $folder->getKey(),
    ]);
});

it('can move a thread between folders with drag and drop', function () use ($setUp) {
    ['user' => $user, 'thread' => $thread] = $setUp();

    $folder = AiThreadFolder::factory()
        ->for($user)
        ->create([
            'application' => AiApplication::PersonalAssistant,
        ]);

    $thread->folder()->associate($folder);
    $thread->save();

    $newFolder = AiThreadFolder::factory()
        ->for($user)
        ->create([
            'application' => AiApplication::PersonalAssistant,
        ]);

    Livewire::test(PersonalAssistant::class)
        ->call('movedThread', $thread->getKey(), $newFolder->getKey())
        ->assertOk();

    assertDatabaseHas(AiThread::class, [
        'id' => $thread->getKey(),
        'folder_id' => $newFolder->getKey(),
    ]);
});

it('can move a thread out of a folder with drag and drop', function () use ($setUp) {
    ['user' => $user, 'thread' => $thread] = $setUp();

    $folder = AiThreadFolder::factory()
        ->for($user)
        ->create([
            'application' => AiApplication::PersonalAssistant,
        ]);

    $thread->folder()->associate($folder);
    $thread->save();

    Livewire::test(PersonalAssistant::class)
        ->call('movedThread', $thread->getKey(), null)
        ->assertOk();

    assertDatabaseHas(AiThread::class, [
        'id' => $thread->getKey(),
        'folder_id' => null,
    ]);
});

it('can not move a thread belonging to a different user in to a folder with drag and drop', function () use ($setUp) {
    ['user' => $user, 'thread' => $thread] = $setUp();

    $thread->user()->associate(User::factory()->create());
    $thread->save();

    $folder = AiThreadFolder::factory()
        ->for($user)
        ->create([
            'application' => AiApplication::PersonalAssistant,
        ]);

    Livewire::test(PersonalAssistant::class)
        ->call('movedThread', $thread->getKey(), $folder->getKey())
        ->assertOk();

    assertDatabaseHas(AiThread::class, [
        'id' => $thread->getKey(),
        'folder_id' => null,
    ]);
});

it('can not move a thread in to a folder belonging to a different user with drag and drop', function () use ($setUp) {
    ['thread' => $thread] = $setUp();

    $folder = AiThreadFolder::factory()
        ->for(User::factory()->create())
        ->create([
            'application' => AiApplication::PersonalAssistant,
        ]);

    Livewire::test(PersonalAssistant::class)
        ->call('movedThread', $thread->getKey(), $folder->getKey())
        ->assertOk();

    assertDatabaseHas(AiThread::class, [
        'id' => $thread->getKey(),
        'folder_id' => null,
    ]);
});

it('can delete a thread', function () use ($setUp) {
    ['thread' => $thread] = $setUp();

    Livewire::test(PersonalAssistant::class)
        ->callAction('deleteThread', arguments: [
            'thread' => $thread->getKey(),
        ]);

    assertSoftDeleted($thread);
});

it('can not delete a thread belonging to a different user', function () use ($setUp) {
    ['assistant' => $assistant] = $setUp();

    $threadBelongingToAnotherUser = AiThread::factory()
        ->for($assistant, 'assistant')
        ->for(User::factory()->create())
        ->create();

    Livewire::test(PersonalAssistant::class)
        ->callAction('deleteThread', arguments: [
            'thread' => $threadBelongingToAnotherUser->getKey(),
        ]);

    assertNotSoftDeleted(AiThread::class, [
        'id' => $threadBelongingToAnotherUser->getKey(),
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
        ->assertDispatched('set-chat-message', content: $prompt->prompt);

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

it('can rename a thread', function () use ($setUp) {
    ['thread' => $thread] = $setUp();

    Livewire::test(PersonalAssistant::class)
        ->callAction('editThread', [
            'name' => $name = AiThread::factory()->make()->name,
        ], arguments: [
            'thread' => $thread->getKey(),
        ])
        ->assertHasNoActionErrors();

    assertDatabaseHas(AiThread::class, [
        'id' => $thread->getKey(),
        'name' => $name,
    ]);
});

it('can not rename a thread without a name', function () use ($setUp) {
    ['thread' => $thread] = $setUp();

    Livewire::test(PersonalAssistant::class)
        ->callAction('editThread', [
            'name' => null,
        ], arguments: [
            'thread' => $thread->getKey(),
        ])
        ->assertHasActionErrors(['name' => 'required']);
});

it('can not rename a thread belonging to a different user', function () use ($setUp) {
    ['thread' => $thread] = $setUp();

    $thread->user()->associate(User::factory()->create());
    $thread->save();

    $oldThreadName = $thread->name;

    Livewire::test(PersonalAssistant::class)
        ->callAction('editThread', [
            'name' => $newThreadName = AiThread::factory()->make()->name,
        ], arguments: [
            'thread' => $thread->getKey(),
        ])
        ->assertHasNoActionErrors();

    assertDatabaseHas(AiThread::class, [
        'id' => $thread->getKey(),
        'name' => $oldThreadName,
    ]);

    expect($oldThreadName)
        ->not->toEqual($newThreadName);
});

it('can clone a thread to a user', function () use ($setUp) {
    Bus::fake();

    ['user' => $user, 'thread' => $thread] = $setUp();

    $otherUser = User::factory()->create();

    Livewire::test(PersonalAssistant::class)
        ->callAction('cloneThread', [
            'targetType' => AiThreadShareTarget::User->value,
            'targetIds' => [$otherUser->getKey()],
        ], arguments: [
            'thread' => $thread->getKey(),
        ])
        ->assertHasNoActionErrors();

    Bus::assertDispatched(PrepareAiThreadCloning::class, function (PrepareAiThreadCloning $job) use ($thread, $otherUser, $user) {
        if (! $job->thread->is($thread)) {
            return false;
        }

        if ($job->targetType !== AiThreadShareTarget::User->value) {
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

it('can clone a thread to a team', function () use ($setUp) {
    Bus::fake();

    ['user' => $user, 'thread' => $thread] = $setUp();

    $team = Team::factory()->create();

    Livewire::test(PersonalAssistant::class)
        ->callAction('cloneThread', [
            'targetType' => AiThreadShareTarget::Team->value,
            'targetIds' => [$team->getKey()],
        ], arguments: [
            'thread' => $thread->getKey(),
        ])
        ->assertHasNoActionErrors();

    Bus::assertDispatched(PrepareAiThreadCloning::class, function (PrepareAiThreadCloning $job) use ($thread, $team, $user) {
        if (! $job->thread->is($thread)) {
            return false;
        }

        if ($job->targetType !== AiThreadShareTarget::Team->value) {
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

it('can not clone a thread without a target type', function () use ($setUp) {
    ['thread' => $thread] = $setUp();

    $otherUser = User::factory()->create();

    Livewire::test(PersonalAssistant::class)
        ->callAction('cloneThread', [
            'targetType' => null,
            'targetIds' => [$otherUser->getKey()],
        ], arguments: [
            'thread' => $thread->getKey(),
        ])
        ->assertHasActionErrors(['targetType' => 'required']);
});

it('can not clone a thread without any targets', function () use ($setUp) {
    ['thread' => $thread] = $setUp();

    Livewire::test(PersonalAssistant::class)
        ->callAction('cloneThread', [
            'targetType' => AiThreadShareTarget::User->value,
            'targetIds' => [],
        ], arguments: [
            'thread' => $thread->getKey(),
        ])
        ->assertHasActionErrors(['targetIds' => 'required']);
});

it('can not clone a thread belonging to a different user', function () use ($setUp) {
    Bus::fake();

    ['thread' => $thread] = $setUp();

    $thread->user()->associate(User::factory()->create());
    $thread->save();

    $otherUser = User::factory()->create();

    Livewire::test(PersonalAssistant::class)
        ->callAction('cloneThread', [
            'targetType' => AiThreadShareTarget::User->value,
            'targetIds' => [$otherUser->getKey()],
        ], arguments: [
            'thread' => $thread->getKey(),
        ]);

    Bus::assertNotDispatched(PrepareAiThreadCloning::class);
});

it('can email a thread to a user', function () use ($setUp) {
    Bus::fake();

    ['user' => $user, 'thread' => $thread] = $setUp();

    $otherUser = User::factory()->create();

    Livewire::test(PersonalAssistant::class)
        ->callAction('emailThread', [
            'targetType' => AiThreadShareTarget::User->value,
            'targetIds' => [$otherUser->getKey()],
        ], arguments: [
            'thread' => $thread->getKey(),
        ])
        ->assertHasNoActionErrors();

    Bus::assertDispatched(PrepareAiThreadEmailing::class, function (PrepareAiThreadEmailing $job) use ($thread, $otherUser, $user) {
        if (! $job->thread->is($thread)) {
            return false;
        }

        if ($job->targetType !== AiThreadShareTarget::User->value) {
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

it('can email a thread to a team', function () use ($setUp) {
    Bus::fake();

    ['user' => $user, 'thread' => $thread] = $setUp();

    $team = Team::factory()->create();

    Livewire::test(PersonalAssistant::class)
        ->callAction('emailThread', [
            'targetType' => AiThreadShareTarget::Team->value,
            'targetIds' => [$team->getKey()],
        ], arguments: [
            'thread' => $thread->getKey(),
        ])
        ->assertHasNoActionErrors();

    Bus::assertDispatched(PrepareAiThreadEmailing::class, function (PrepareAiThreadEmailing $job) use ($thread, $team, $user) {
        if (! $job->thread->is($thread)) {
            return false;
        }

        if ($job->targetType !== AiThreadShareTarget::Team->value) {
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

it('can not email a thread without a target type', function () use ($setUp) {
    ['thread' => $thread] = $setUp();

    $otherUser = User::factory()->create();

    Livewire::test(PersonalAssistant::class)
        ->callAction('emailThread', [
            'targetType' => null,
            'targetIds' => [$otherUser->getKey()],
        ], arguments: [
            'thread' => $thread->getKey(),
        ])
        ->assertHasActionErrors(['targetType' => 'required']);
});

it('can not email a thread without any targets', function () use ($setUp) {
    ['thread' => $thread] = $setUp();

    Livewire::test(PersonalAssistant::class)
        ->callAction('emailThread', [
            'targetType' => AiThreadShareTarget::User->value,
            'targetIds' => [],
        ], arguments: [
            'thread' => $thread->getKey(),
        ])
        ->assertHasActionErrors(['targetIds' => 'required']);
});

it('can not email a thread belonging to a different user', function () use ($setUp) {
    Bus::fake();

    ['thread' => $thread] = $setUp();

    $thread->user()->associate(User::factory()->create());
    $thread->save();

    $otherUser = User::factory()->create();

    Livewire::test(PersonalAssistant::class)
        ->callAction('emailThread', [
            'targetType' => AiThreadShareTarget::User->value,
            'targetIds' => [$otherUser->getKey()],
        ], arguments: [
            'thread' => $thread->getKey(),
        ]);

    Bus::assertNotDispatched(PrepareAiThreadEmailing::class);
});
