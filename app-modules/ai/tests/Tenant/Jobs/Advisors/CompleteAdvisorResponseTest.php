<?php

/*
<COPYRIGHT>

    Copyright © 2016-2026, Canyon GBS LLC. All rights reserved.

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
use AdvisingApp\Ai\Exceptions\AiResponseToCompleteDoesNotExistException;
use AdvisingApp\Ai\Jobs\Advisors\CompleteAdvisorResponse;
use AdvisingApp\Ai\Models\AiAssistant;
use AdvisingApp\Ai\Models\AiMessage;
use AdvisingApp\Ai\Models\AiThread;
use Illuminate\Support\Facades\Event;

use function Tests\asSuperAdmin;

it('completes the last response', function () {
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
        ->has(
            AiMessage::factory()
                ->for(auth()->user())
                ->state(['created_at' => now()->subMinutes(5)]),
            relationship: 'messages',
        )
        ->has(
            AiMessage::factory()->state([
                'created_at' => now()->subMinutes(4),
                'user_id' => null,
            ]),
            relationship: 'messages',
        )
        ->has(
            AiMessage::factory()
                ->for(auth()->user())
                ->state(['created_at' => now()->subMinutes(3)]),
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
            AiMessage::factory()
                ->for(auth()->user())
                ->state(['created_at' => now()->subMinute()]),
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

    dispatch(new CompleteAdvisorResponse($thread));

    $messages = AiMessage::query()->oldest()->get();

    expect($messages->count())
        ->toBe(6);

    $response = $messages->last();

    expect($response->content)
        ->not->toBe($originalResponseContent)
        ->toStartWith($originalResponseContent);

    Event::assertDispatched(AdvisorMessageChunk::class);
    Event::assertDispatched(AdvisorMessageFinished::class);
});

it('strips the appended ... when completing the last response', function () {
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
        ->has(
            AiMessage::factory()
                ->for(auth()->user())
                ->state(['created_at' => now()->subMinute()]),
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

    dispatch(new CompleteAdvisorResponse($thread));

    $messages = AiMessage::query()->oldest()->get();

    expect($messages->count())
        ->toBe(2);

    $response = $messages->last();

    expect($response->content)
        ->not->toBe('foo...bar...baz ')
        ->toStartWith('foo...bar...baz ')
        ->not->toBe('foo...bar...baz...')
        ->not->toStartWith('foo...bar...baz...');

    Event::assertDispatched(AdvisorMessageChunk::class);
    Event::assertDispatched(AdvisorMessageFinished::class);
});

it('throws an exception if the latest response does not exist', function () {
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

    dispatch(new CompleteAdvisorResponse($thread));
})->throws(AiResponseToCompleteDoesNotExistException::class);
