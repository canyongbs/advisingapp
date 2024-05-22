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

use function Pest\Laravel\get;
use function Tests\asSuperAdmin;

use AdvisingApp\Ai\Enums\AiModel;
use AdvisingApp\Ai\Models\AiThread;
use AdvisingApp\Ai\Models\AiMessage;
use AdvisingApp\Ai\Models\AiAssistant;
use AdvisingApp\Ai\Enums\AiApplication;

it('fetches information about a thread', function () {
    asSuperAdmin();

    $thread = AiThread::factory()
        ->for(AiAssistant::factory()->create([
            'application' => AiApplication::PersonalAssistant,
            'is_default' => true,
            'model' => AiModel::Test,
        ]), 'assistant')
        ->for(auth()->user())
        ->create();

    get(route('ai.threads.show', ['thread' => $thread]))
        ->assertSuccessful();
});

it('lists messages in a thread', function () {
    asSuperAdmin();

    $thread = AiThread::factory()
        ->for(AiAssistant::factory()->create([
            'application' => AiApplication::PersonalAssistant,
            'is_default' => true,
            'model' => AiModel::Test,
        ]), 'assistant')
        ->for(auth()->user())
        ->has(AiMessage::factory()->count(3), 'messages')
        ->create();

    get(route('ai.threads.show', ['thread' => $thread]))
        ->assertJsonCount(3, 'messages');
});

it('converts new lines to HTML in messages sent by users', function () {
    asSuperAdmin();

    $thread = AiThread::factory()
        ->for(AiAssistant::factory()->create([
            'application' => AiApplication::PersonalAssistant,
            'is_default' => true,
            'model' => AiModel::Test,
        ]), 'assistant')
        ->for(auth()->user())
        ->has(AiMessage::factory()->for(auth()->user())->state([
            'content' => 'Hello, world!' . PHP_EOL . 'How are you?',
        ]), 'messages')
        ->create();

    get(route('ai.threads.show', ['thread' => $thread]))
        ->assertJsonFragment([
            'content' => 'Hello, world!<br />' . PHP_EOL . 'How are you?',
        ]);
});

it('removes HTML tags from messages sent by users', function () {
    asSuperAdmin();

    $thread = AiThread::factory()
        ->for(AiAssistant::factory()->create([
            'application' => AiApplication::PersonalAssistant,
            'is_default' => true,
            'model' => AiModel::Test,
        ]), 'assistant')
        ->for(auth()->user())
        ->has(AiMessage::factory()->for(auth()->user())->state([
            'content' => '<script>alert("Hello, world!")</script>',
        ]), 'messages')
        ->create();

    get(route('ai.threads.show', ['thread' => $thread]))
        ->assertJsonFragment([
            'content' => 'alert(&#34;Hello, world!&#34;)',
        ]);
});

it('converts messages sent by assistants into Markdown', function () {
    asSuperAdmin();

    $thread = AiThread::factory()
        ->for(AiAssistant::factory()->create([
            'application' => AiApplication::PersonalAssistant,
            'is_default' => true,
            'model' => AiModel::Test,
        ]), 'assistant')
        ->for(auth()->user())
        ->has(AiMessage::factory()->state([
            'content' => 'Hello, world!',
        ]), 'messages')
        ->create();

    get(route('ai.threads.show', ['thread' => $thread]))
        ->assertJsonFragment([
            'content' => '<p>Hello, world!</p>' . PHP_EOL,
        ]);
});

it('removes unsafe HTML from messages sent by assistants', function () {
    asSuperAdmin();

    $thread = AiThread::factory()
        ->for(AiAssistant::factory()->create([
            'application' => AiApplication::PersonalAssistant,
            'is_default' => true,
            'model' => AiModel::Test,
        ]), 'assistant')
        ->for(auth()->user())
        ->has(AiMessage::factory()->state([
            'content' => '<script>alert("Hello, world!")</script>',
        ]), 'messages')
        ->create();

    get(route('ai.threads.show', ['thread' => $thread]))
        ->assertJsonFragment([
            'content' => '&lt;script&gt;alert(&#34;Hello, world!&#34;)&lt;/script&gt;' . PHP_EOL,
        ]);
});

it('lists users involved in a thread once', function () {
    asSuperAdmin();

    $thread = AiThread::factory()
        ->for(AiAssistant::factory()->create([
            'application' => AiApplication::PersonalAssistant,
            'is_default' => true,
            'model' => AiModel::Test,
        ]), 'assistant')
        ->for(auth()->user())
        ->has(AiMessage::factory()->for(auth()->user()), 'messages')
        ->has(AiMessage::factory()->for($anotherUser = User::factory()->create())->count(3), 'messages')
        ->create();

    get(route('ai.threads.show', ['thread' => $thread]))
        ->assertJsonCount(2, 'users')
        ->assertJsonFragment([
            'name' => auth()->user()->name,
            'avatar_url' => auth()->user()->getFilamentAvatarUrl(),
        ])
        ->assertJsonFragment([
            'name' => $anotherUser->name,
            'avatar_url' => $anotherUser->getFilamentAvatarUrl(),
        ]);
});

it('prevents users who do not own the thread from fetching it', function () {
    asSuperAdmin();

    $thread = AiThread::factory()
        ->for(AiAssistant::factory()->create([
            'application' => AiApplication::PersonalAssistant,
            'is_default' => true,
            'model' => AiModel::Test,
        ]), 'assistant')
        ->for(User::factory()->create())
        ->create();

    get(route('ai.threads.show', ['thread' => $thread]))
        ->assertForbidden();
});
