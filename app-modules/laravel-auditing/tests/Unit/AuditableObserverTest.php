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

namespace Assist\LaravelAuditing\Tests;

use Illuminate\Support\Facades\Event;
use Assist\LaravelAuditing\Models\Audit;
use Assist\LaravelAuditing\AuditableObserver;
use Assist\LaravelAuditing\Events\DispatchAudit;
use Assist\LaravelAuditing\Tests\Models\Article;
use Assist\LaravelAuditing\Events\DispatchingAudit;

class AuditableObserverTest extends AuditingTestCase
{
    /**
     * @test
     *
     * @dataProvider auditableObserverDispatchTestProvider
     *
     * @param mixed $eventMethod
     */
    public function itWillCancelTheAuditDispatchingFromAnEventListener($eventMethod)
    {
        Event::fake(
            [
                DispatchAudit::class,
            ]
        );

        Event::listen(DispatchingAudit::class, function () {
            return false;
        });

        $observer = new AuditableObserver();
        $model = factory(Article::class)->create();

        $observer->$eventMethod($model);

        $this->assertNull(Audit::first());

        Event::assertNotDispatched(DispatchAudit::class);
    }

    /**
     * @test
     *
     * @dataProvider auditableObserverDispatchTestProvider
     */
    public function itDispatchesTheCorrectEvents(string $eventMethod)
    {
        Event::fake();

        $observer = new AuditableObserver();
        $model = factory(Article::class)->create();

        $observer->$eventMethod($model);

        Event::assertDispatched(DispatchingAudit::class, function ($event) use ($model) {
            return $event->model->is($model);
        });

        Event::assertDispatched(DispatchAudit::class, function ($event) use ($model) {
            return $event->model->is($model);
        });
    }

    /**
     * @group AuditableObserver::retrieved
     * @group AuditableObserver::created
     * @group AuditableObserver::updated
     * @group AuditableObserver::deleted
     * @group AuditableObserver::restoring
     * @group AuditableObserver::restored
     *
     * @test
     *
     * @dataProvider auditableObserverTestProvider
     *
     * @param string $eventMethod
     * @param bool   $expectedBefore
     * @param bool   $expectedAfter
     */
    public function itExecutesTheAuditorSuccessfully(string $eventMethod, bool $expectedBefore, bool $expectedAfter)
    {
        $observer = new AuditableObserver();
        $model = factory(Article::class)->create();

        $this->assertSame($expectedBefore, $observer::$restoring);

        $observer->$eventMethod($model);

        $this->assertSame($expectedAfter, $observer::$restoring);
    }

    /**
     * @return array
     */
    public static function auditableObserverTestProvider(): array
    {
        return [
            [
                'retrieved',
                false,
                false,
            ],
            [
                'created',
                false,
                false,
            ],
            [
                'updated',
                false,
                false,
            ],
            [
                'deleted',
                false,
                false,
            ],
            [
                'restoring',
                false,
                true,
            ],
            [
                'restored',
                true,
                false,
            ],
        ];
    }

    /**
     * @return array
     */
    public static function auditableObserverDispatchTestProvider(): array
    {
        return [
            [
                'created',
            ],
            [
                'updated',
            ],
            [
                'deleted',
            ],
            [
                'restored',
            ],
        ];
    }
}
