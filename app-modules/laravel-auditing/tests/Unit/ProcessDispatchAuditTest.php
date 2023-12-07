<?php

/*
<COPYRIGHT>

    Copyright © 2022-2023, Canyon GBS LLC. All rights reserved.

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
