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
use AdvisingApp\Ai\Events\Advisors\AdvisorThreadRenamed;
use AdvisingApp\Ai\Jobs\Advisors\GenerateAiThreadName;
use AdvisingApp\Ai\Models\AiAssistant;
use AdvisingApp\Ai\Models\AiMessage;
use AdvisingApp\Ai\Models\AiThread;
use AdvisingApp\Ai\Services\TestAiService;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Exceptions;

use function Tests\asSuperAdmin;

function createNamingTestThread(): AiThread
{
    $assistant = AiAssistant::factory()->create([
        'application' => AiAssistantApplication::Test,
        'is_default' => true,
        'model' => AiModel::Test,
    ]);

    return AiThread::factory()
        ->for($assistant, 'assistant')
        ->for(auth()->user())
        ->has(AiMessage::factory()->for(auth()->user()), relationship: 'messages')
        ->has(AiMessage::factory()->state(['user_id' => null]), relationship: 'messages')
        ->create([
            'name' => 'New Chat 8/13/26 @ 7:10 AM',
            'named_by_user_at' => null,
        ]);
}

it('only allows a single attempt, so a broadcast failure does not trigger a billed retry', function () {
    asSuperAdmin();

    expect((new GenerateAiThreadName(createNamingTestThread()))->tries)
        ->toBe(1);
});

it('renames a thread using the AI service', function () {
    asSuperAdmin();

    Event::fake([AdvisorThreadRenamed::class]);

    $thread = createNamingTestThread();

    bindFakeAiService(fn (): string => 'AI Generated Name');

    app(GenerateAiThreadName::class, ['thread' => $thread])->handle();

    $thread->refresh();

    expect($thread->name)
        ->toBe('AI Generated Name')
        ->and($thread->named_by_user_at)
        ->toBeNull();

    Event::assertDispatched(AdvisorThreadRenamed::class, fn (AdvisorThreadRenamed $event) => $event->thread->is($thread) && $event->thread->name === $thread->name);
});

it('saves the name and reports the exception if broadcasting AdvisorThreadRenamed fails', function () {
    asSuperAdmin();

    Exceptions::fake();

    config(['broadcasting.default' => 'null']);

    Event::listen(AdvisorThreadRenamed::class, function (): void {
        throw new Exception('The broadcast connection is down.');
    });

    $thread = createNamingTestThread();

    bindFakeAiService(fn (): string => 'AI Generated Name');

    app(GenerateAiThreadName::class, ['thread' => $thread])->handle();

    $thread->refresh();

    expect($thread->name)
        ->toBe('AI Generated Name');

    Exceptions::assertReported(fn (Exception $exception): bool => $exception->getMessage() === 'The broadcast connection is down.');
});

it('does not rename a thread that has already been renamed by the user', function () {
    asSuperAdmin();

    Event::fake([AdvisorThreadRenamed::class]);

    $thread = createNamingTestThread();
    $thread->update([
        'name' => 'My Custom Name',
        'named_by_user_at' => now(),
    ]);

    app(GenerateAiThreadName::class, ['thread' => $thread])->handle();

    $thread->refresh();

    expect($thread->name)
        ->toBe('My Custom Name');

    Event::assertNotDispatched(AdvisorThreadRenamed::class);
});

it('does not call the AI service for a thread that was deleted before the job runs', function () {
    asSuperAdmin();

    Event::fake([AdvisorThreadRenamed::class]);

    $thread = createNamingTestThread();

    $wasCalled = false;

    bindFakeAiService(function () use (&$wasCalled): string {
        $wasCalled = true;

        return 'AI Generated Name';
    });

    $thread->delete();

    app(GenerateAiThreadName::class, ['thread' => $thread])->handle();

    expect($wasCalled)
        ->toBeFalse();

    Event::assertNotDispatched(AdvisorThreadRenamed::class);
});

it('does not rename a thread when the AI service returns a blank name', function () {
    asSuperAdmin();

    Event::fake([AdvisorThreadRenamed::class]);

    $thread = createNamingTestThread();

    bindFakeAiService(fn (): string => "  \n  ");

    app(GenerateAiThreadName::class, ['thread' => $thread])->handle();

    $thread->refresh();

    expect($thread->name)
        ->toBe('New Chat 8/13/26 @ 7:10 AM')
        ->and($thread->named_by_user_at)
        ->toBeNull();

    Event::assertNotDispatched(AdvisorThreadRenamed::class);
});

/**
 * @param Closure(): string $complete
 */
function bindFakeAiService(Closure $complete): void
{
    app()->bind(TestAiService::class, fn () => new class ($complete) extends TestAiService {
        public function __construct(
            protected Closure $complete,
        ) {}

        public function complete(string $prompt, string $content, bool $shouldTrack = true): string
        {
            return ($this->complete)();
        }
    });
}

it('reports the exception and leaves the thread unnamed when the AI service fails', function () {
    asSuperAdmin();

    Event::fake([AdvisorThreadRenamed::class]);
    Exceptions::fake();

    $thread = createNamingTestThread();

    bindFakeAiService(function (): string {
        throw new Exception('The AI service is down.');
    });

    app(GenerateAiThreadName::class, ['thread' => $thread])->handle();

    $thread->refresh();

    expect($thread->name)
        ->toBe('New Chat 8/13/26 @ 7:10 AM')
        ->and($thread->saved_at)
        ->toBeNull();

    Exceptions::assertReported(Exception::class);

    Event::assertNotDispatched(AdvisorThreadRenamed::class);
});

it('does not rename a thread that got renamed by the user while the AI service was generating a name', function () {
    asSuperAdmin();

    Event::fake([AdvisorThreadRenamed::class]);

    $thread = createNamingTestThread();

    bindFakeAiService(function () use ($thread): string {
        $thread->update([
            'name' => 'Renamed By User Mid-Flight',
            'named_by_user_at' => now(),
        ]);

        return 'AI Generated Name';
    });

    app(GenerateAiThreadName::class, ['thread' => $thread])->handle();

    $thread->refresh();

    expect($thread->name)
        ->toBe('Renamed By User Mid-Flight');

    Event::assertNotDispatched(AdvisorThreadRenamed::class);
});

it('does not rename a thread that got deleted while the AI service was generating a name', function () {
    asSuperAdmin();

    Event::fake([AdvisorThreadRenamed::class]);

    $thread = createNamingTestThread();

    bindFakeAiService(function () use ($thread): string {
        $thread->delete();

        return 'AI Generated Name';
    });

    app(GenerateAiThreadName::class, ['thread' => $thread])->handle();

    $thread->refresh();

    expect($thread->name)
        ->toBe('New Chat 8/13/26 @ 7:10 AM')
        ->and($thread->trashed())
        ->toBeTrue();

    Event::assertNotDispatched(AdvisorThreadRenamed::class);
});
