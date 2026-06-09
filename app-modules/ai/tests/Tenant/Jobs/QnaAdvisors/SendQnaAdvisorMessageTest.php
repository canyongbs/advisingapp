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

use AdvisingApp\Ai\Enums\AiModel;
use AdvisingApp\Ai\Events\QnaAdvisors\QnaAdvisorMessageChunk;
use AdvisingApp\Ai\Jobs\QnaAdvisors\SendQnaAdvisorMessage;
use AdvisingApp\Ai\Models\QnaAdvisor;
use AdvisingApp\Ai\Models\QnaAdvisorMessage;
use AdvisingApp\Ai\Models\QnaAdvisorThread;
use AdvisingApp\Ai\Services\TestAiService;
use AdvisingApp\Ai\Support\StreamingChunks\Finish;
use AdvisingApp\Ai\Support\StreamingChunks\Text;
use Illuminate\Support\Facades\Event;

it('surfaces the rate limit reset time and does not persist a partial response when the stream is rate limited', function () {
    Event::fake([QnaAdvisorMessageChunk::class]);

    $advisor = QnaAdvisor::factory()->create(['model' => AiModel::Test]);
    $thread = QnaAdvisorThread::factory()->for($advisor, 'advisor')->create();

    $service = Mockery::mock(TestAiService::class)->makePartial();
    $service->shouldReceive('streamRaw')->andReturn(function () {
        yield new Text('A partial answer that gets cut off');

        yield new Finish(rateLimitResetsAt: now()->addSeconds(30));
    });
    app()->instance(TestAiService::class, $service);

    dispatch_sync(new SendQnaAdvisorMessage($advisor, $thread, 'Hello'));

    // The user's message is persisted, but the rate-limited advisor response is not.
    expect(QnaAdvisorMessage::query()->where('is_advisor', false)->count())->toBe(1);
    expect(QnaAdvisorMessage::query()->where('is_advisor', true)->count())->toBe(0);

    Event::assertDispatched(
        QnaAdvisorMessageChunk::class,
        fn (QnaAdvisorMessageChunk $event): bool => $event->thread->is($thread)
            && $event->isComplete === false
            && $event->rateLimitResetsAt !== null,
    );
});

it('completes and persists the response when the stream finishes normally', function () {
    Event::fake([QnaAdvisorMessageChunk::class]);

    $advisor = QnaAdvisor::factory()->create(['model' => AiModel::Test]);
    $thread = QnaAdvisorThread::factory()->for($advisor, 'advisor')->create();

    $service = Mockery::mock(TestAiService::class)->makePartial();
    $service->shouldReceive('streamRaw')->andReturn(function () {
        yield new Text('Here is a complete answer.');

        yield new Finish();
    });
    app()->instance(TestAiService::class, $service);

    dispatch_sync(new SendQnaAdvisorMessage($advisor, $thread, 'Hello'));

    $response = QnaAdvisorMessage::query()->where('is_advisor', true)->first();

    expect($response)->not->toBeNull()
        ->and($response->content)->toBe('Here is a complete answer.');

    Event::assertDispatched(
        QnaAdvisorMessageChunk::class,
        fn (QnaAdvisorMessageChunk $event): bool => $event->isComplete === true,
    );
});
