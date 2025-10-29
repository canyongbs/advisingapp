<?php

use AdvisingApp\Ai\Events\QnaAdvisors\EndQnaAdvisorThread;
use AdvisingApp\Ai\Jobs\QnaAdvisors\AutomaticallyEndQnaAdvisors;
use AdvisingApp\Ai\Models\QnaAdvisorMessage;
use AdvisingApp\Ai\Models\QnaAdvisorThread;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Queue;

use function Pest\Laravel\travelTo;

it('will only run for advisors that have had no activity in over an hour', function() {
    Queue::fake();
    
    $thread = QnaAdvisorThread::factory()->has(QnaAdvisorMessage::factory(), 'messages')->create();

    expect($thread->finished_at)->toBeNull();

    travelTo(now()->addMinutes(61));

    (new AutomaticallyEndQnaAdvisors())->handle();

    expect($thread->refresh()->finished_at)->not()->toBeNull();
});

it('will not run for advisors that have had activity within the last hour', function () {
    Queue::fake();
    
    $thread = QnaAdvisorThread::factory()->has(QnaAdvisorMessage::factory(), 'messages')->create();

    expect($thread->finished_at)->toBeNull();

    travelTo(now()->addMinutes(59));

    (new AutomaticallyEndQnaAdvisors())->handle();

    expect($thread->refresh()->finished_at)->toBeNull();
});

it('dispatches websocket event when it automatically finishes a thread', function () {
    Queue::fake();
    Event::fake();
    
    $thread = QnaAdvisorThread::factory()->has(QnaAdvisorMessage::factory(), 'messages')->create();

    travelTo(now()->addMinutes(61));

    (new AutomaticallyEndQnaAdvisors())->handle();

    Event::assertDispatched(EndQnaAdvisorThread::class);
});