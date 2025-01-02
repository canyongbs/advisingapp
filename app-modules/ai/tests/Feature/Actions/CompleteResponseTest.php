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

use AdvisingApp\Ai\Actions\CompleteResponse;
use AdvisingApp\Ai\Enums\AiApplication;
use AdvisingApp\Ai\Enums\AiModel;
use AdvisingApp\Ai\Exceptions\AiAssistantArchivedException;
use AdvisingApp\Ai\Exceptions\AiResponseToCompleteDoesNotExistException;
use AdvisingApp\Ai\Exceptions\AiThreadLockedException;
use AdvisingApp\Ai\Models\AiAssistant;
use AdvisingApp\Ai\Models\AiMessage;
use AdvisingApp\Ai\Models\AiThread;
use AdvisingApp\Report\Enums\TrackedEventType;
use AdvisingApp\Report\Jobs\RecordTrackedEvent;
use Illuminate\Support\Facades\Queue;

use function Tests\asSuperAdmin;

it('completes the last response', function () {
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
        ->has(
            AiMessage::factory()->for(auth()->user())->state(['created_at' => now()->subMinutes(2)]),
            relationship: 'messages',
        )
        ->has(
            AiMessage::factory()->state([
                'created_at' => now()->subMinutes(2),
                'user_id' => null,
            ]),
            relationship: 'messages',
        )
        ->has(
            AiMessage::factory()->for(auth()->user())->state(['created_at' => now()->subMinute()]),
            relationship: 'messages',
        )
        ->has(
            AiMessage::factory()->state([
                'created_at' => now()->subMinute(),
                'user_id' => null,
            ]),
            relationship: 'messages',
        )
        ->has(
            AiMessage::factory()->for(auth()->user()),
            relationship: 'messages',
        )
        ->has(
            AiMessage::factory()->state([
                'user_id' => null,
            ]),
            relationship: 'messages',
        )
        ->create();

    expect(AiMessage::count())
        ->toBe(6);

    $originalResponseContent = $thread->messages()
        ->whereNull('user_id')
        ->latest()
        ->first()
        ->content;

    $responseStream = app(CompleteResponse::class)($thread);

    $streamedContent = '';

    foreach ($responseStream() as $responseContent) {
        $streamedContent .= $responseContent;
    }

    $messages = AiMessage::all();

    expect($messages->count())
        ->toBe(6);

    $response = $messages->last();

    expect($response->content)
        ->toBe("{$originalResponseContent}{$streamedContent}");

    expect(Queue::pushed(RecordTrackedEvent::class))
        ->toHaveCount(1)
        ->each
        ->toHaveProperties(['type' => TrackedEventType::AiExchange]);
});

it('strips the appended ... when completing the last response', function () {
    asSuperAdmin();

    $assistant = AiAssistant::factory()->create([
        'application' => AiApplication::Test,
        'is_default' => true,
        'model' => AiModel::Test,
    ]);

    $thread = AiThread::factory()
        ->for($assistant, 'assistant')
        ->for(auth()->user())
        ->has(
            AiMessage::factory()->for(auth()->user()),
            relationship: 'messages',
        )
        ->has(
            AiMessage::factory()->state([
                'content' => 'foo...bar...baz...',
                'user_id' => null,
            ]),
            relationship: 'messages',
        )
        ->create();

    expect(AiMessage::count())
        ->toBe(2);

    $responseStream = app(CompleteResponse::class)($thread);

    $streamedContent = '';

    foreach ($responseStream() as $responseContent) {
        $streamedContent .= $responseContent;
    }

    $messages = AiMessage::all();

    expect($messages->count())
        ->toBe(2);

    $response = $messages->last();

    expect($response->content)
        ->toBe("foo...bar...baz {$streamedContent}");
});

it('throws an exception if the latest response does not exist', function () {
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

    iterator_to_array(app(CompleteResponse::class)($thread)());
})->throws(AiResponseToCompleteDoesNotExistException::class);

it('throws an exception if the thread is locked', function () {
    asSuperAdmin();

    $thread = AiThread::factory()->make([
        'locked_at' => now(),
    ]);

    iterator_to_array(app(CompleteResponse::class)($thread)());
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

    app(CompleteResponse::class)($thread)();

    iterator_to_array(app(CompleteResponse::class)($thread)());
})->throws(AiAssistantArchivedException::class);
