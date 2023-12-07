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

namespace Assist\LaravelAuditing\Tests\Functional;

use Carbon\Carbon;
use InvalidArgumentException;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Event;
use Assist\LaravelAuditing\Models\Audit;
use Assist\LaravelAuditing\Events\Auditing;
use Illuminate\Foundation\Testing\WithFaker;
use Assist\LaravelAuditing\Tests\Models\User;
use Assist\LaravelAuditing\Events\AuditCustom;
use Assist\LaravelAuditing\Tests\Models\Article;
use Assist\LaravelAuditing\Tests\Models\Category;
use Assist\LaravelAuditing\Tests\AuditingTestCase;
use Assist\LaravelAuditing\Tests\Models\ArticleExcludes;
use Assist\LaravelAuditing\Tests\fixtures\TenantResolver;

class AuditingTest extends AuditingTestCase
{
    use WithFaker;

    /**
     * @test
     */
    public function itWillNotAuditModelsWhenRunningFromTheConsole()
    {
        $this->app['config']->set('audit.console', false);

        factory(User::class)->create();

        $this->assertSame(1, User::query()->count());
        $this->assertSame(0, Audit::query()->count());
    }

    /**
     * @test
     */
    public function itWillAuditModelsWhenRunningFromTheConsole()
    {
        $this->app['config']->set('audit.console', true);

        factory(User::class)->create();

        $this->assertSame(1, User::query()->count());
        $this->assertSame(1, Audit::query()->count());
    }

    /**
     * @test
     */
    public function itWillAlwaysAuditModelsWhenNotRunningFromTheConsole()
    {
        App::shouldReceive('runningInConsole')
            ->andReturn(false);

        $this->app['config']->set('audit.console', false);

        factory(User::class)->create();

        $this->assertSame(1, User::query()->count());
        $this->assertSame(1, Audit::query()->count());
    }

    /**
     * @test
     */
    public function itWillNotAuditTheRetrievingEvent()
    {
        $this->app['config']->set('audit.console', true);

        factory(User::class)->create();

        $this->assertSame(1, User::query()->count());
        $this->assertSame(1, Audit::query()->count());

        User::first();

        $this->assertSame(1, Audit::query()->count());
        $this->assertSame(1, User::query()->count());
    }

    /**
     * @test
     */
    public function itWillAuditTheRetrievingEvent()
    {
        $this->app['config']->set('audit.console', true);
        $this->app['config']->set('audit.events', [
            'created',
            'retrieved',
        ]);

        factory(User::class)->create();

        $this->assertSame(1, User::query()->count());
        $this->assertSame(1, Audit::query()->count());

        User::first();
        $this->assertSame(1, User::query()->count());
        $this->assertSame(2, Audit::query()->count());
    }

    /**
     * @test
     */
    public function itWillAuditTheRetrievedEvent()
    {
        $this->app['config']->set('audit.events', [
            'retrieved',
        ]);

        factory(Article::class)->create([
            'title' => 'How To Audit Eloquent Models',
            'content' => 'N/A',
            'published_at' => null,
            'reviewed' => 0,
        ]);

        Article::first();

        $audit = Audit::first();

        $this->assertEmpty($audit->old_values);

        $this->assertEmpty($audit->new_values);
    }

    /**
     * @test
     */
    public function itWillAuditTheCreatedEvent()
    {
        $this->app['config']->set('audit.events', [
            'created',
        ]);

        factory(Article::class)->create([
            'title' => 'How To Audit Eloquent Models',
            'content' => 'N/A',
            'published_at' => null,
            'reviewed' => 0,
        ]);

        $audit = Audit::first();

        $this->assertEmpty($audit->old_values);

        self::Assert()::assertArraySubset([
            'title' => 'How To Audit Eloquent Models',
            'content' => 'N/A',
            'published_at' => null,
            'reviewed' => 0,
            'id' => 1,
        ], $audit->new_values, true);
    }

    /**
     * @test
     */
    public function itWillAuditTheUpdatedEvent()
    {
        $this->app['config']->set('audit.events', [
            'updated',
        ]);

        $article = factory(Article::class)->create([
            'title' => 'How To Audit Eloquent Models',
            'content' => 'N/A',
            'published_at' => null,
            'reviewed' => 0,
        ]);

        $now = Carbon::now();

        $article->update([
            'content' => 'First step: install the laravel-auditing package.',
            'published_at' => $now,
            'reviewed' => 1,
        ]);

        $audit = Audit::first();

        self::Assert()::assertArraySubset([
            'content' => 'N/A',
            'published_at' => null,
            'reviewed' => 0,
        ], $audit->old_values, true);

        self::Assert()::assertArraySubset([
            'content' => Article::contentMutate('First step: install the laravel-auditing package.'),
            'published_at' => $now->toDateTimeString(),
            'reviewed' => 1,
        ], $audit->new_values, true);
    }

    /**
     * @test
     */
    public function itWillAuditTheDeletedEvent()
    {
        $this->app['config']->set('audit.events', [
            'deleted',
        ]);

        $article = factory(Article::class)->create([
            'title' => 'How To Audit Eloquent Models',
            'content' => 'N/A',
            'published_at' => null,
            'reviewed' => 0,
        ]);

        $article->delete();

        $audit = Audit::first();

        self::Assert()::assertArraySubset([
            'title' => 'How To Audit Eloquent Models',
            'content' => 'N/A',
            'published_at' => null,
            'reviewed' => 0,
            'id' => 1,
        ], $audit->old_values, true);

        $this->assertEmpty($audit->new_values);
    }

    /**
     * @test
     */
    public function itWillAuditTheRestoredEvent()
    {
        $this->app['config']->set('audit.events', [
            'restored',
        ]);

        $article = factory(Article::class)->create([
            'title' => 'How To Audit Eloquent Models',
            'content' => 'N/A',
            'published_at' => null,
            'reviewed' => 0,
        ]);

        $article->delete();
        $article->restore();

        $audit = Audit::first();

        $this->assertEmpty($audit->old_values);

        self::Assert()::assertArraySubset([
            'title' => 'How To Audit Eloquent Models',
            'content' => 'N/A',
            'published_at' => null,
            'reviewed' => 0,
            'id' => 1,
        ], $audit->new_values, true);
    }

    /**
     * @test
     */
    public function itWillKeepAllAudits()
    {
        $this->app['config']->set('audit.threshold', 0);
        $this->app['config']->set('audit.events', [
            'updated',
        ]);

        $article = factory(Article::class)->create([
            'reviewed' => 1,
        ]);

        foreach (range(0, 99) as $count) {
            $article->update([
                'reviewed' => ($count % 2),
            ]);
        }

        $this->assertSame(100, $article->audits()->count());
    }

    /**
     * @test
     */
    public function itWillRemoveOlderAuditsAboveTheThreshold()
    {
        $this->app['config']->set('audit.threshold', 10);
        $this->app['config']->set('audit.events', [
            'updated',
        ]);

        $article = factory(Article::class)->create([
            'reviewed' => 1,
        ]);

        foreach (range(0, 99) as $count) {
            $article->update([
                'reviewed' => ($count % 2),
            ]);
        }

        $this->assertSame(10, $article->audits()->count());
    }

    /**
     * @test
     */
    public function itWillNotAuditDueToUnsupportedDriver()
    {
        $this->app['config']->set('audit.driver', 'foo');

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Driver [foo] not supported.');

        factory(Article::class)->create();
    }

    /**
     * @test
     */
    public function itWillAuditUsingTheDefaultDriver()
    {
        $this->app['config']->set('audit.driver', null);

        factory(Article::class)->create([
            'title' => 'How To Audit Using The Fallback Driver',
            'content' => 'N/A',
            'published_at' => null,
            'reviewed' => 0,
        ]);

        $audit = Audit::first();

        $this->assertEmpty($audit->old_values);

        self::Assert()::assertArraySubset([
            'title' => 'How To Audit Using The Fallback Driver',
            'content' => 'N/A',
            'published_at' => null,
            'reviewed' => 0,
            'id' => 1,
        ], $audit->new_values, true);
    }

    /**
     * @test
     */
    public function itWillCancelTheAuditFromAnEventListener()
    {
        Event::listen(Auditing::class, function () {
            return false;
        });

        factory(Article::class)->create();

        $this->assertNull(Audit::first());
    }

    /**
     * @test
     */
    public function itDisablesAndEnablesAuditingBackAgain()
    {
        // Auditing is enabled by default
        $this->assertFalse(Article::$auditingDisabled);

        factory(Article::class)->create();

        $this->assertSame(1, Article::count());
        $this->assertSame(1, Audit::count());

        // Disable Auditing
        Article::disableAuditing();
        $this->assertTrue(Article::$auditingDisabled);

        factory(Article::class)->create();

        $this->assertSame(2, Article::count());
        $this->assertSame(1, Audit::count());

        // Re-enable Auditing
        Article::enableAuditing();
        $this->assertFalse(Article::$auditingDisabled);

        factory(Article::class)->create();

        $this->assertSame(2, Audit::count());
        $this->assertSame(3, Article::count());
    }

    /**
     * @test
     */
    public function itDisablesAndEnablesAuditingBackAgainViaFacade()
    {
        // Auditing is enabled by default
        $this->assertFalse(Article::$auditingDisabled);

        Article::disableAuditing();

        factory(Article::class)->create();

        $this->assertSame(1, Article::count());
        $this->assertSame(0, Audit::count());

        // Enable Auditing
        Article::enableAuditing();
        $this->assertFalse(Article::$auditingDisabled);

        factory(Article::class)->create();

        $this->assertSame(2, Article::count());
        $this->assertSame(1, Audit::count());
    }

    /**
     * @test
     *
     * @return void
     */
    public function itHandlesJsonColumnsCorrectly()
    {
        $article = factory(Article::class)->create(['config' => ['articleIsGood' => true, 'authorsJob' => 'vampire']]);
        $article->refresh();

        $article->config = ['articleIsGood' => false, 'authorsJob' => 'vampire'];
        $article->save();

        /** @var Audit $audit */
        $audit = $article->audits()->skip(1)->first();
        $this->assertSame(false, $audit->getModified()['config']['new']['articleIsGood']);
        $this->assertSame(true, $audit->getModified()['config']['old']['articleIsGood']);
    }

    /**
     * @return void
     *
     * @test
     */
    public function canAddAdditionalResolver()
    {
        // added new resolver
        $this->app['config']->set('audit.resolvers.tenant_id', TenantResolver::class);

        $article = factory(Article::class)->create();

        $this->assertTrue(true);
        $audit = $article->audits()->first();
        $this->assertSame(1, (int) $audit->tenant_id);
    }

    /**
     * @return void
     *
     * @test
     */
    public function canDisableResolver()
    {
        // added new resolver
        $this->app['config']->set('audit.resolvers.ip_address', null);

        $article = factory(Article::class)->create();

        $audit = $article->audits()->first();
        $this->assertEmpty($audit->ip_address);
    }

    /**
     * @test
     *
     * @return void
     */
    public function itWillExcludeIfGlobalExcludeIsSet()
    {
        $this->app['config']->set('audit.exclude', ['content']);

        $article = new Article();
        $article->title = $this->faker->unique()->sentence;
        $article->content = $this->faker->unique()->paragraph(6);
        $article->published_at = null;
        $article->reviewed = 0;
        $article->save();
        $this->assertArrayNotHasKey('content', $article->audits()->first()->getModified());
    }

    /**
     * @test
     *
     * @return void
     */
    public function localExcludeOverridesGlobalExclude()
    {
        $this->app['config']->set('audit.exclude', ['content']);

        $article = new ArticleExcludes();
        $article->title = $this->faker->unique()->sentence;
        $article->content = $this->faker->unique()->paragraph(6);
        $article->published_at = null;
        $article->reviewed = 0;
        $article->save();
        $this->assertArrayHasKey('content', $article->audits()->first()->getModified());
        $this->assertArrayNotHasKey('title', $article->audits()->first()->getModified());
    }

    /**
     * @test
     *
     */
    public function itWillNotAuditModelsWhenValuesAreEmpty()
    {
        $this->app['config']->set('audit.empty_values', false);

        $article = new ArticleExcludes();
        $article->auditExclude = [];
        $article->title = $this->faker->unique()->sentence;
        $article->content = $this->faker->unique()->paragraph(6);
        $article->published_at = null;
        $article->reviewed = 0;
        $article->save();

        $article->auditExclude = [
            'reviewed',
        ];

        $article->reviewed = 1;
        $article->save();

        $this->assertSame(1, Article::query()->count());
        $this->assertSame(1, Audit::query()->count());
    }

    /**
     * @return void
     *
     * @test
     */
    public function itWillAuditRetrievedEventEvenIfAuditEmptyIsDisabled()
    {
        $this->app['config']->set('audit.empty_values', false);
        $this->app['config']->set('audit.allowed_empty_values', ['retrieved']);
        $this->app['config']->set('audit.events', [
            'created',
            'retrieved',
        ]);

        $this->app['config']->set('audit.empty_values', false);

        /** @var Article $model */
        factory(Article::class)->create();

        Article::find(1);

        $this->assertSame(2, Audit::query()->count());
    }

    /**
     * @test
     */
    public function itWillAuditModelsWhenValuesAreEmpty()
    {
        $model = factory(Article::class)->create([
            'reviewed' => 0,
        ]);

        $model->reviewed = 1;
        $model->save();

        $this->assertSame(1, Article::query()->count());
        $this->assertSame(2, Audit::query()->count());
    }

    /**
     * @test
     *
     * @return void
     */
    public function itWillAuditAttach()
    {
        $firstCategory = factory(Category::class)->create();
        $secondCategory = factory(Category::class)->create();
        $article = factory(Article::class)->create();

        $article->auditAttach('categories', $firstCategory);
        $article->auditAttach('categories', $secondCategory);
        $lastArticleAudit = $article->audits->last()->getModified()['categories'];

        $this->assertSame($firstCategory->name, $article->categories->first()->name);
        $this->assertSame(0, count($lastArticleAudit['old']));
        $this->assertSame(1, count($lastArticleAudit['new']));
        $this->assertSame($secondCategory->name, $lastArticleAudit['new'][0]['name']);
    }

    /**
     * @test
     *
     * @return void
     */
    public function itWillAuditSync()
    {
        $firstCategory = factory(Category::class)->create();
        $secondCategory = factory(Category::class)->create();
        $article = factory(Article::class)->create();

        $article->categories()->attach($firstCategory);

        $no_of_audits_before = Audit::where('auditable_type', Article::class)->count();
        $categoryBefore = $article->categories()->first()->getKey();

        $article->auditSync('categories', [$secondCategory->getKey()]);

        $no_of_audits_after = Audit::where('auditable_type', Article::class)->count();
        $categoryAfter = $article->categories()->first()->getKey();

        $this->assertSame($firstCategory->getKey(), $categoryBefore);
        $this->assertSame($secondCategory->getKey(), $categoryAfter);
        $this->assertNotSame($categoryBefore, $categoryAfter);
        $this->assertGreaterThan($no_of_audits_before, $no_of_audits_after);
    }

    /**
     * @test
     *
     * @return void
     */
    public function itWillAuditDetach()
    {
        $firstCategory = factory(Category::class)->create();
        $secondCategory = factory(Category::class)->create();
        $article = factory(Article::class)->create();

        $article->categories()->attach($firstCategory);
        $article->categories()->attach($secondCategory);

        $no_of_audits_before = Audit::where('auditable_type', Article::class)->count();
        $categoryBefore = $article->categories()->first()->getKey();

        $article->auditDetach('categories', [$firstCategory->getKey()]);

        $no_of_audits_after = Audit::where('auditable_type', Article::class)->count();
        $categoryAfter = $article->categories()->first()->getKey();

        $this->assertSame($firstCategory->getKey(), $categoryBefore);
        $this->assertSame($secondCategory->getKey(), $categoryAfter);
        $this->assertNotSame($categoryBefore, $categoryAfter);
        $this->assertGreaterThan($no_of_audits_before, $no_of_audits_after);
    }

    /**
     * @test
     *
     * @return void
     */
    public function itWillAuditSyncWithoutChanges()
    {
        $firstCategory = factory(Category::class)->create();
        $article = factory(Article::class)->create();

        $article->categories()->attach($firstCategory);

        $no_of_audits_before = Audit::where('auditable_type', Article::class)->count();
        $categoryBefore = $article->categories()->first()->getKey();

        $article->auditSync('categories', [$firstCategory->getKey()]);

        $no_of_audits_after = Audit::where('auditable_type', Article::class)->count();
        $categoryAfter = $article->categories()->first()->getKey();

        $this->assertSame($firstCategory->getKey(), $categoryBefore);
        $this->assertSame($firstCategory->getKey(), $categoryAfter);
        $this->assertSame($categoryBefore, $categoryAfter);
        $this->assertGreaterThan($no_of_audits_before, $no_of_audits_after);
    }

    /**
     * @test
     *
     * @return void
     */
    public function itWillAuditSyncWhenSkippingEmptyValues()
    {
        $this->app['config']->set('audit.empty_values', false);

        $firstCategory = factory(Category::class)->create();
        $secondCategory = factory(Category::class)->create();
        $article = factory(Article::class)->create();

        $article->categories()->attach($firstCategory);

        $no_of_audits_before = Audit::where('auditable_type', Article::class)->count();
        $categoryBefore = $article->categories()->first()->getKey();

        $article->auditSync('categories', [$secondCategory->getKey()]);

        $no_of_audits_after = Audit::where('auditable_type', Article::class)->count();
        $categoryAfter = $article->categories()->first()->getKey();

        $this->assertSame($firstCategory->getKey(), $categoryBefore);
        $this->assertSame($secondCategory->getKey(), $categoryAfter);
        $this->assertNotSame($categoryBefore, $categoryAfter);
        $this->assertGreaterThan($no_of_audits_before, $no_of_audits_after);
    }

    /**
     * @test
     *
     * @return void
     */
    public function itWillNotAuditSyncWhenSkippingEmptyValuesAndNoChangesMade()
    {
        $this->app['config']->set('audit.empty_values', false);

        $firstCategory = factory(Category::class)->create();
        $article = factory(Article::class)->create();

        $article->categories()->attach($firstCategory);

        $no_of_audits_before = Audit::where('auditable_type', Article::class)->count();
        $categoryBefore = $article->categories()->first()->getKey();

        $article->auditSync('categories', [$firstCategory->getKey()]);

        $no_of_audits_after = Audit::where('auditable_type', Article::class)->count();
        $categoryAfter = $article->categories()->first()->getKey();

        $this->assertSame($firstCategory->getKey(), $categoryBefore);
        $this->assertSame($firstCategory->getKey(), $categoryAfter);
        $this->assertSame($categoryBefore, $categoryAfter);
        $this->assertSame($no_of_audits_before, $no_of_audits_after);
    }

    /**
     * @test
     *
     * @return void
     */
    public function itWillNotAuditAttachWhenSkippingEmptyValuesAndNoChangesMade()
    {
        $this->app['config']->set('audit.empty_values', false);

        $firstCategory = factory(Category::class)->create();
        $article = factory(Article::class)->create();

        $article->categories()->attach($firstCategory);

        $no_of_audits_before = Audit::where('auditable_type', Article::class)->count();
        $categoryBefore = $article->categories()->first()->getKey();

        $article->auditAttach('categories', [$firstCategory->getKey()]);

        $no_of_audits_after = Audit::where('auditable_type', Article::class)->count();
        $categoryAfter = $article->categories()->first()->getKey();

        $this->assertSame($firstCategory->getKey(), $categoryBefore);
        $this->assertSame($firstCategory->getKey(), $categoryAfter);
        $this->assertSame($categoryBefore, $categoryAfter);
        $this->assertSame($no_of_audits_before, $no_of_audits_after);
    }

    /**
     * @test
     *
     * @return void
     */
    public function itWillNotAuditDetachWhenSkippingEmptyValuesAndNoChangesMade()
    {
        $this->app['config']->set('audit.empty_values', false);

        $firstCategory = factory(Category::class)->create();
        $secondCategory = factory(Category::class)->create();
        $article = factory(Article::class)->create();

        $article->categories()->attach($firstCategory);

        $no_of_audits_before = Audit::where('auditable_type', Article::class)->count();
        $categoryBefore = $article->categories()->first()->getKey();

        $article->auditDetach('categories', [$secondCategory->getKey()]);

        $no_of_audits_after = Audit::where('auditable_type', Article::class)->count();
        $categoryAfter = $article->categories()->first()->getKey();

        $this->assertSame($firstCategory->getKey(), $categoryBefore);
        $this->assertSame($firstCategory->getKey(), $categoryAfter);
        $this->assertSame($categoryBefore, $categoryAfter);
        $this->assertSame($no_of_audits_before, $no_of_audits_after);
    }

    /**
     * @test
     *
     * @return void
     */
    public function canAuditAnyCustomEvent()
    {
        $article = factory(Article::class)->create();
        $article->auditEvent = 'whateverYouWant';
        $article->isCustomEvent = true;
        $article->auditCustomOld = [
            'customExample' => 'Anakin Skywalker',
        ];
        $article->auditCustomNew = [
            'customExample' => 'Darth Vader',
        ];
        Event::dispatch(AuditCustom::class, [$article]);

        $this->assertDatabaseHas(config('audit.drivers.database.table', 'audits'), [
            'auditable_id' => $article->id,
            'auditable_type' => Article::class,
            'event' => 'whateverYouWant',
            'new_values' => '{"customExample":"Darth Vader"}',
            'old_values' => '{"customExample":"Anakin Skywalker"}',
        ]);
    }
}
