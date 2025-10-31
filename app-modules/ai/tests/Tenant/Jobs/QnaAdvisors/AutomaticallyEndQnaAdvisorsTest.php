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

use AdvisingApp\Ai\Events\QnaAdvisors\EndQnaAdvisorThread;
use AdvisingApp\Ai\Jobs\QnaAdvisors\AutomaticallyEndQnaAdvisors;
use AdvisingApp\Ai\Models\QnaAdvisorMessage;
use AdvisingApp\Ai\Models\QnaAdvisorThread;
use Illuminate\Support\Facades\Event;

it('will only run for advisors that have had no activity in over an hour', function () {
    $thread = QnaAdvisorThread::factory()
        ->has(
            QnaAdvisorMessage::factory()->state([
                'created_at' => now()->subHours(2),
            ]),
            'messages'
        )
        ->create();

    expect($thread->finished_at)->toBeNull();

    (new AutomaticallyEndQnaAdvisors())->handle();

    $thread->refresh();

    expect($thread->finished_at)->not()->toBeNull();
});

it('will not run for advisors that have had activity within the last hour', function () {
    $thread = QnaAdvisorThread::factory()
        ->has(
            QnaAdvisorMessage::factory()->state([
                'created_at' => now()->subMinutes(30),
            ]),
            'messages'
        )
        ->create();

    expect($thread->finished_at)->toBeNull();

    (new AutomaticallyEndQnaAdvisors())->handle();

    $thread->refresh();

    expect($thread->finished_at)->toBeNull();
});

it('dispatches websocket event when it automatically finishes a thread', function () {
    Event::fake();

    $thread = QnaAdvisorThread::factory()
        ->has(
            QnaAdvisorMessage::factory()->state([
                'created_at' => now()->subHours(2),
            ]),
            'messages'
        )
        ->create();

    (new AutomaticallyEndQnaAdvisors())->handle();

    Event::assertDispatched(EndQnaAdvisorThread::class);
});
