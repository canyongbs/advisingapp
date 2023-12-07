<?php

namespace Assist\LaravelAuditing\Tests\Unit;

use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Queue;
use Illuminate\Events\CallQueuedListener;
use Assist\LaravelAuditing\Events\DispatchAudit;
use Assist\LaravelAuditing\Tests\Models\Article;
use Assist\LaravelAuditing\Tests\AuditingTestCase;
use Assist\LaravelAuditing\Listeners\ProcessDispatchAudit;

class ProcessDispatchAuditTest extends AuditingTestCase
{
    /**
     * @test
     */
    public function itIsListeningToTheCorrectEvent()
    {
        Event::fake();

        Event::assertListening(
            DispatchAudit::class,
            ProcessDispatchAudit::class
        );
    }

    /**
     * @test
     */
    public function itGetsProperlyQueued()
    {
        Queue::fake();

        $model = factory(Article::class)->create();

        DispatchAudit::dispatch($model);

        Queue::assertPushed(CallQueuedListener::class, function ($job) use ($model) {
            return $job->class == ProcessDispatchAudit::class
                && $job->data[0] instanceof DispatchAudit
                && $job->data[0]->model->is($model);
        });
    }

    /**
     * @test
     */
    public function itCanHaveConnectionAndQueueSet()
    {
        $this->app['config']->set('audit.queue.connection', 'redis');
        $this->app['config']->set('audit.queue.queue', 'audits');
        $this->app['config']->set('audit.queue.delay', 60);

        Queue::fake();

        $model = factory(Article::class)->create();

        DispatchAudit::dispatch($model);

        Queue::assertPushedOn('audits', CallQueuedListener::class, function ($job) use ($model) {
            $instantiatedJob = new $job->class();

            return $job->class == ProcessDispatchAudit::class
                && $job->data[0] instanceof DispatchAudit
                && $job->data[0]->model->is($model)
                && $instantiatedJob->viaConnection() == 'redis'
                && $instantiatedJob->withDelay(new DispatchAudit($model)) == 60;
        });
    }
}
