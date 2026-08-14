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
use AdvisingApp\Ai\Jobs\Advisors\GenerateAiThreadName;
use AdvisingApp\Ai\Models\AiAssistant;
use AdvisingApp\Ai\Models\AiMessage;
use AdvisingApp\Ai\Models\AiThread;
use AdvisingApp\Ai\Services\TestAiService;
use AdvisingApp\Report\Enums\TrackedEventType;
use AdvisingApp\Report\Jobs\RecordTrackedEvent;
use App\Features\AiThreadAutoNamingFeature;
use Illuminate\Support\Facades\Bus;

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

it('renames a thread using the AI service and marks it as saved', function () {
    AiThreadAutoNamingFeature::activate();

    asSuperAdmin();

    Bus::fake([RecordTrackedEvent::class]);

    $thread = createNamingTestThread();

    app(GenerateAiThreadName::class, ['thread' => $thread])->handle();

    $thread->refresh();

    expect($thread->name)
        ->not->toBe('New Chat 8/13/26 @ 7:10 AM')
        ->and($thread->name)
        ->not->toBeEmpty()
        ->and($thread->saved_at)
        ->not->toBeNull()
        ->and($thread->named_by_user_at)
        ->toBeNull();

    Bus::assertDispatched(RecordTrackedEvent::class, fn (RecordTrackedEvent $job) => $job->type === TrackedEventType::AiThreadSaved);
});

it('does not rename a thread that has already been renamed by the user', function () {
    AiThreadAutoNamingFeature::activate();

    asSuperAdmin();

    Bus::fake([RecordTrackedEvent::class]);

    $thread = createNamingTestThread();
    $thread->update([
        'name' => 'My Custom Name',
        'named_by_user_at' => now(),
    ]);

    app(GenerateAiThreadName::class, ['thread' => $thread])->handle();

    $thread->refresh();

    expect($thread->name)
        ->toBe('My Custom Name');

    Bus::assertNotDispatched(RecordTrackedEvent::class);
});

it('does nothing when the auto naming feature is inactive', function () {
    AiThreadAutoNamingFeature::deactivate();

    asSuperAdmin();

    Bus::fake([RecordTrackedEvent::class]);

    $thread = createNamingTestThread();

    app(GenerateAiThreadName::class, ['thread' => $thread])->handle();

    $thread->refresh();

    expect($thread->name)
        ->toBe('New Chat 8/13/26 @ 7:10 AM');

    Bus::assertNotDispatched(RecordTrackedEvent::class);
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
    AiThreadAutoNamingFeature::activate();

    asSuperAdmin();

    Bus::fake([RecordTrackedEvent::class]);

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

    Bus::assertNotDispatched(RecordTrackedEvent::class);
});

it('does not rename a thread that got renamed by the user while the AI service was generating a name', function () {
    AiThreadAutoNamingFeature::activate();

    asSuperAdmin();

    Bus::fake([RecordTrackedEvent::class]);

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

    Bus::assertNotDispatched(RecordTrackedEvent::class);
});
