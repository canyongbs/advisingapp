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

use AdvisingApp\Ai\Enums\AiAssistantApplication;
use AdvisingApp\Ai\Enums\AiModel;
use AdvisingApp\Ai\Events\Advisors\AdvisorMessageChunk;
use AdvisingApp\Ai\Events\Advisors\AdvisorMessageFinished;
use AdvisingApp\Ai\Jobs\Advisors\GenerateAiThreadName;
use AdvisingApp\Ai\Jobs\Advisors\SendAdvisorMessage;
use AdvisingApp\Ai\Models\AiAssistant;
use AdvisingApp\Ai\Models\AiMessage;
use AdvisingApp\Ai\Models\AiThread;
use AdvisingApp\Ai\Models\Prompt;
use AdvisingApp\Report\Enums\TrackedEventType;
use AdvisingApp\Report\Jobs\RecordTrackedEvent;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Event;

use function Pest\Laravel\travelBack;
use function Pest\Laravel\travelTo;
use function Tests\asSuperAdmin;

it('sends a message', function () {
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

    $content = AiMessage::factory()->make()->content;

    expect(AiMessage::count())
        ->toBe(0);

    dispatch(new SendAdvisorMessage(
        $thread,
        $content,
    ));

    $messages = AiMessage::all();

    expect($messages->count())
        ->toBe(2);

    expect($messages->first())
        ->content->toBe($content)
        ->thread->getKey()->toBe($thread->getKey())
        ->user->getKey()->toBe(auth()->user()->getKey());

    $response = $messages->last();

    expect($response) /** @phpstan-ignore method.nonObject */
        ->thread->getKey()->toBe($thread->getKey())
        ->user->toBeNull();

    Event::assertDispatched(AdvisorMessageChunk::class);
    Event::assertDispatched(AdvisorMessageFinished::class);
});

it('builds smart prompt content from the editable instructions setting', function () {
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

    $prompt = Prompt::factory()->create([
        'is_smart' => true,
        'title' => 'Draft An Email',
        'description' => 'Helps you write an email',
    ]);

    dispatch(new SendAdvisorMessage(
        $thread,
        $prompt,
    ));

    $message = AiMessage::query()
        ->whereNotNull('prompt_id')
        ->first();

    expect($message->content)
        ->toContain($prompt->title)
        ->toContain($prompt->type->title)
        ->toContain($prompt->description)
        ->toEndWith($prompt->prompt);
});

it('dispatches GenerateAiThreadName exactly once, right after the third user message and response', function () {
    Bus::fake([GenerateAiThreadName::class]);

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

    dispatch(new SendAdvisorMessage($thread, 'Message 1'));

    Bus::assertNotDispatched(GenerateAiThreadName::class);

    dispatch(new SendAdvisorMessage($thread, 'Message 2'));

    Bus::assertNotDispatched(GenerateAiThreadName::class);

    dispatch(new SendAdvisorMessage($thread, 'Message 3'));

    Bus::assertDispatchedTimes(GenerateAiThreadName::class, 1);

    dispatch(new SendAdvisorMessage($thread, 'Message 4'));

    Bus::assertDispatchedTimes(GenerateAiThreadName::class, 1);
});

it('does not dispatch GenerateAiThreadName when the thread has already been renamed by the user', function () {
    Bus::fake([GenerateAiThreadName::class]);

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
        ->create([
            'named_by_user_at' => now(),
        ]);

    dispatch(new SendAdvisorMessage($thread, 'Message 1'));
    dispatch(new SendAdvisorMessage($thread, 'Message 2'));
    dispatch(new SendAdvisorMessage($thread, 'Message 3'));

    Bus::assertNotDispatched(GenerateAiThreadName::class);
});

it('sets saved_at and dispatches the AiThreadSaved tracked event when the first message is persisted', function () {
    Bus::fake([RecordTrackedEvent::class]);

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
        ->create([
            'saved_at' => null,
        ]);

    dispatch(new SendAdvisorMessage($thread, 'Message 1'));

    expect($thread->fresh()->saved_at)
        ->not->toBeNull();

    Bus::assertDispatchedTimes(
        fn (RecordTrackedEvent $job): bool => $job->type === TrackedEventType::AiThreadSaved,
        1,
    );
});

it('does not update saved_at or dispatch the AiThreadSaved tracked event again on subsequent messages', function () {
    Bus::fake([RecordTrackedEvent::class]);

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
        ->create([
            'saved_at' => null,
        ]);

    dispatch(new SendAdvisorMessage($thread, 'Message 1'));

    $savedAt = $thread->fresh()->saved_at;

    try {
        travelTo(now()->addMinute());

        dispatch(new SendAdvisorMessage($thread, 'Message 2'));
    } finally {
        travelBack();
    }

    expect($thread->fresh()->saved_at)
        ->toEqual($savedAt);

    Bus::assertDispatchedTimes(
        fn (RecordTrackedEvent $job): bool => $job->type === TrackedEventType::AiThreadSaved,
        1,
    );
});

it('does not overwrite an already saved_at thread', function () {
    Bus::fake([RecordTrackedEvent::class]);

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

    $savedAt = now()->subDays(10);

    $thread = AiThread::factory()
        ->for($assistant, 'assistant')
        ->for(auth()->user())
        ->create([
            'saved_at' => $savedAt,
        ]);

    $savedAt = $thread->fresh()->saved_at;

    dispatch(new SendAdvisorMessage($thread, 'Message 1'));

    expect($thread->fresh()->saved_at)
        ->toEqual($savedAt);

    Bus::assertNotDispatched(
        RecordTrackedEvent::class,
        fn (RecordTrackedEvent $job): bool => $job->type === TrackedEventType::AiThreadSaved,
    );
});
