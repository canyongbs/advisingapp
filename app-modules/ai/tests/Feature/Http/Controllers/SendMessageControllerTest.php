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
use Mockery\MockInterface;

use function Pest\Laravel\post;
use function Tests\asSuperAdmin;

use AdvisingApp\Ai\Enums\AiModel;
use AdvisingApp\Ai\Models\AiThread;
use AdvisingApp\Ai\Models\AiMessage;
use AdvisingApp\Ai\Models\AiAssistant;
use AdvisingApp\Ai\Actions\SendMessage;
use AdvisingApp\Ai\Enums\AiApplication;

it('sends a message to a thread', function () {
    asSuperAdmin();

    $responseContent = AiMessage::factory()->make()->content;

    /** @phpstan-ignore-next-line */
    $this->mock(
        SendMessage::class,
        fn (MockInterface $mock) => $mock
            ->shouldReceive('__invoke')->once()
            ->andReturn($responseContent),
    );

    $thread = AiThread::factory()
        ->for(AiAssistant::factory()->create([
            'application' => AiApplication::Test,
            'is_default' => true,
            'model' => AiModel::Test,
        ]), 'assistant')
        ->for(auth()->user())
        ->create();

    post(route('ai.threads.messages.send', ['thread' => $thread]), [
        'content' => AiMessage::factory()->make()->content,
    ])
        ->assertSuccessful()
        ->assertJson([
            'content' => $responseContent,
        ]);
});

it('returns a message if the assistant fails', function () {
    asSuperAdmin();

    /** @phpstan-ignore-next-line */
    $this->mock(
        SendMessage::class,
        fn (MockInterface $mock) => $mock
            ->shouldReceive('__invoke')->once()
            ->andThrow(new Exception('Failed to send message')),
    );

    $thread = AiThread::factory()
        ->for(AiAssistant::factory()->create([
            'application' => AiApplication::Test,
            'is_default' => true,
            'model' => AiModel::Test,
        ]), 'assistant')
        ->for(auth()->user())
        ->create();

    post(route('ai.threads.messages.send', ['thread' => $thread]), [
        'content' => AiMessage::factory()->make()->content,
    ])
        ->assertServiceUnavailable()
        ->assertJson([
            'message' => 'The assistant has failed. Please retry later.',
        ]);
});

it('prevents users who do not own the thread from sending messages to it', function () {
    asSuperAdmin();

    $thread = AiThread::factory()
        ->for(AiAssistant::factory()->create([
            'application' => AiApplication::Test,
            'is_default' => true,
            'model' => AiModel::Test,
        ]), 'assistant')
        ->for(User::factory()->create())
        ->create();

    post(route('ai.threads.messages.send', ['thread' => $thread]), [
        'content' => AiMessage::factory()->make()->content,
    ])
        ->assertForbidden();
});

todo('prevents users from sending an empty message');

todo('prevents users from sending a non-string message');

todo('prevents users from sending a message over 1000 characters');
