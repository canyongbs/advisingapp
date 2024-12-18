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

use AdvisingApp\Ai\Actions\SendMessage;
use AdvisingApp\Ai\Enums\AiApplication;
use AdvisingApp\Ai\Enums\AiModel;
use AdvisingApp\Ai\Exceptions\AiAssistantArchivedException;
use AdvisingApp\Ai\Exceptions\AiThreadLockedException;
use AdvisingApp\Ai\Models\AiAssistant;
use AdvisingApp\Ai\Models\AiMessage;
use AdvisingApp\Ai\Models\AiMessageFile;
use AdvisingApp\Ai\Models\AiThread;
use AdvisingApp\Report\Enums\TrackedEventType;
use AdvisingApp\Report\Jobs\RecordTrackedEvent;
use Illuminate\Support\Facades\Queue;

use function Tests\asSuperAdmin;

it('sends a message', function () {
    asSuperAdmin();

    $assistant = AiAssistant::factory()->create([
        'application' => AiApplication::Test,
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

    $responseStream = app(SendMessage::class)($thread, $content);

    $streamedContent = '';

    foreach ($responseStream() as $responseContent) {
        $streamedContent .= $responseContent;
    }

    $messages = AiMessage::all();

    expect($messages->count())
        ->toBe(2);

    expect($messages->first())
        ->content->toBe($content)
        ->thread->getKey()->toBe($thread->getKey())
        ->user->getKey()->toBe(auth()->user()->getKey());

    $response = $messages->last();

    expect($response)
        ->thread->getKey()->toBe($thread->getKey())
        ->user->toBeNull();

    expect($streamedContent)->toBe($response->content);
});

it('sends a message with a file', function () {
    asSuperAdmin();

    $assistant = AiAssistant::factory()->create([
        'application' => AiApplication::Test,
        'is_default' => true,
        'model' => AiModel::Test,
    ]);

    $thread = AiThread::factory()
        ->for($assistant, 'assistant')
        ->for(auth()->user())
        ->create();

    $content = AiMessage::factory()->make()->content;
    $files = ['some-file'];

    expect(AiMessage::count())
        ->toBe(0);

    expect(AiMessageFile::count())
        ->toBe(0);

    $responseStream = app(SendMessage::class)($thread, $content, $files);

    $streamedContent = '';

    foreach ($responseStream() as $responseContent) {
        $streamedContent .= $responseContent;
    }

    $messages = AiMessage::all();
    $messageFiles = AiMessageFile::all();

    expect($messages->count())
        ->toBe(2);

    expect($messageFiles->count())
        ->toBe(1);

    expect($messages->first())
        ->content->toBe($content)
        ->thread->getKey()->toBe($thread->getKey())
        ->user->getKey()->toBe(auth()->user()->getKey())
        ->files->first()->getKey()->toBe($messageFiles->first()->getKey());

    $response = $messages->last();

    expect($response)
        ->thread->getKey()->toBe($thread->getKey())
        ->user->toBeNull();

    expect($streamedContent)->toBe($response->content);
});

it('throws an exception if the thread is locked', function () {
    asSuperAdmin();

    $thread = AiThread::factory()->make([
        'locked_at' => now(),
    ]);

    iterator_to_array(app(SendMessage::class)($thread, 'Hello, world!')());
})->throws(AiThreadLockedException::class);

it('throws an exception if the assistant is archived', function () {
    asSuperAdmin();

    $thread = AiThread::factory()
        ->for(AiAssistant::factory()->state([
            'application' => AiApplication::Test,
            'archived_at' => now(),
            'model' => AiModel::Test,
        ]), 'assistant')
        ->for(auth()->user())
        ->create();

    app(SendMessage::class)($thread, 'Hello, world!')();

    iterator_to_array(app(SendMessage::class)($thread, 'Hello, world!')());
})->throws(AiAssistantArchivedException::class);

it('dispatches tracking for AiExchange for both sent message and response', function () {
    Queue::fake();

    asSuperAdmin();

    $assistant = AiAssistant::factory()->create([
        'application' => AiApplication::Test,
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

    $responseStream = app(SendMessage::class)($thread, $content);

    $streamedContent = '';

    foreach ($responseStream() as $responseContent) {
        $streamedContent .= $responseContent;
    }

    $messages = AiMessage::all();

    expect($messages->count())
        ->toBe(2);

    expect(Queue::pushed(RecordTrackedEvent::class))
        ->toHaveCount(2)
        ->each
        ->toHaveProperties(['type' => TrackedEventType::AiExchange]);
});
