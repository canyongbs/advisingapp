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
use AdvisingApp\Ai\Events\Advisors\AdvisorMessageChunk;
use AdvisingApp\Ai\Events\Advisors\AdvisorMessageFinished;
use AdvisingApp\Ai\Jobs\Advisors\RetryAdvisorMessage;
use AdvisingApp\Ai\Models\AiAssistant;
use AdvisingApp\Ai\Models\AiMessage;
use AdvisingApp\Ai\Models\AiThread;
use App\Models\User;
use Illuminate\Support\Facades\Event;

use function Tests\asSuperAdmin;

it('retries a message', function () {
    Event::fake([
        AdvisorMessageChunk::class,
        AdvisorMessageFinished::class,
    ]);

    asSuperAdmin();

    $assistant = AiAssistant::factory()->create([
        'application' => AiAssistantApplication::Test,
        'is_default' => true,
        'model' => AiModel::Test,
    ]);

    $thread = AiThread::factory()
        ->for($assistant, 'assistant')
        ->for(auth()->user())
        ->create();

    $messageContent = AiMessage::factory()->make()->content;

    expect(AiMessage::count())
        ->toBe(0);

    dispatch(new RetryAdvisorMessage($thread, $messageContent));

    $messages = AiMessage::all();

    expect($messages->count())
        ->toBe(2);

    expect($messages->first())
        ->content->toBe($messageContent)
        ->thread->getKey()->toBe($thread->getKey())
        ->user->getKey()->toBe(auth()->user()->getKey());

    $response = $messages->last();

    expect($response)
        ->thread->getKey()->toBe($thread->getKey())
        ->user->toBeNull();

    Event::assertDispatched(AdvisorMessageChunk::class);
    Event::assertDispatched(AdvisorMessageFinished::class);
});

it('does not create a new message if the most recent one has the same content', function () {
    Event::fake([
        AdvisorMessageChunk::class,
        AdvisorMessageFinished::class,
    ]);

    asSuperAdmin();

    $assistant = AiAssistant::factory()->create([
        'application' => AiAssistantApplication::Test,
        'is_default' => true,
        'model' => AiModel::Test,
    ]);

    $messageContent = AiMessage::factory()->make()->content;

    $thread = AiThread::factory()
        ->for($assistant, 'assistant')
        ->for(auth()->user())
        ->has(AiMessage::factory()->state([
            'content' => $messageContent,
        ])->for(auth()->user()), 'messages')
        ->create();

    expect(AiMessage::count())
        ->toBe(1);

    dispatch(new RetryAdvisorMessage($thread, $messageContent));

    $messages = AiMessage::all();

    expect($messages->count())
        ->toBe(2);

    expect($messages->first())
        ->content->toBe($messageContent)
        ->thread->getKey()->toBe($thread->getKey())
        ->user->getKey()->toBe(auth()->user()->getKey());

    $response = $messages->last();

    expect($response)
        ->thread->getKey()->toBe($thread->getKey())
        ->user->toBeNull();

    Event::assertDispatched(AdvisorMessageChunk::class);
    Event::assertDispatched(AdvisorMessageFinished::class);
});

it('does not match messages with the same content sent by other users in the same thread', function () {
    Event::fake([
        AdvisorMessageChunk::class,
        AdvisorMessageFinished::class,
    ]);

    asSuperAdmin();

    $assistant = AiAssistant::factory()->create([
        'application' => AiAssistantApplication::Test,
        'is_default' => true,
        'model' => AiModel::Test,
    ]);

    $messageContent = AiMessage::factory()->make()->content;

    $thread = AiThread::factory()
        ->for($assistant, 'assistant')
        ->for(auth()->user())
        ->has(AiMessage::factory()
            ->for(User::factory()->create())
            ->state([
                'content' => $messageContent,
            ]), 'messages')
        ->create();

    expect(AiMessage::count())
        ->toBe(1);

    dispatch(new RetryAdvisorMessage($thread, $messageContent));

    expect(AiMessage::count())
        ->toBe(3);

    Event::assertDispatched(AdvisorMessageChunk::class);
    Event::assertDispatched(AdvisorMessageFinished::class);
});

it('does not match messages with the same content belonging to other threads', function () {
    Event::fake([
        AdvisorMessageChunk::class,
        AdvisorMessageFinished::class,
    ]);

    asSuperAdmin();

    $assistant = AiAssistant::factory()->create([
        'application' => AiAssistantApplication::Test,
        'is_default' => true,
        'model' => AiModel::Test,
    ]);

    $messageContent = AiMessage::factory()->make()->content;

    AiThread::factory()
        ->for($assistant, 'assistant')
        ->for(auth()->user())
        ->has(AiMessage::factory()
            ->for(auth()->user())
            ->state([
                'content' => $messageContent,
            ]), 'messages')
        ->create();

    $thread = AiThread::factory()
        ->for($assistant, 'assistant')
        ->for(auth()->user())
        ->create();

    expect(AiMessage::count())
        ->toBe(1);

    dispatch(new RetryAdvisorMessage($thread, $messageContent));

    expect(AiMessage::count())
        ->toBe(3);

    Event::assertDispatched(AdvisorMessageChunk::class);
    Event::assertDispatched(AdvisorMessageFinished::class);
});

it('does not match messages with different content', function () {
    Event::fake([
        AdvisorMessageChunk::class,
        AdvisorMessageFinished::class,
    ]);

    asSuperAdmin();

    $assistant = AiAssistant::factory()->create([
        'application' => AiAssistantApplication::Test,
        'is_default' => true,
        'model' => AiModel::Test,
    ]);

    $messageContent = AiMessage::factory()->make()->content;

    $thread = AiThread::factory()
        ->for($assistant, 'assistant')
        ->for(auth()->user())
        ->has(AiMessage::factory()->for(auth()->user()), 'messages')
        ->create();

    expect(AiMessage::count())
        ->toBe(1);

    dispatch(new RetryAdvisorMessage($thread, $messageContent));

    expect(AiMessage::count())
        ->toBe(3);

    Event::assertDispatched(AdvisorMessageChunk::class);
    Event::assertDispatched(AdvisorMessageFinished::class);
});
