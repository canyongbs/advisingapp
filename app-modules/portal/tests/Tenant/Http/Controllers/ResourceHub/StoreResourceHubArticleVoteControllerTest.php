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

use AdvisingApp\Portal\Models\PortalGuest;
use AdvisingApp\Portal\Models\ResourceHubArticleVote;
use AdvisingApp\Portal\Settings\PortalSettings;
use AdvisingApp\Prospect\Models\Prospect;
use AdvisingApp\ResourceHub\Models\ResourceHubArticle;
use AdvisingApp\StudentDataModel\Models\Student;
use App\Features\ResourceHubArticleFeedbackFeature;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\assertDatabaseMissing;
use function Pest\Laravel\postJson;

beforeEach(function () {
    $portalSettings = app(PortalSettings::class);
    $portalSettings->resource_hub_portal_enabled = true;
    $portalSettings->save();
});

it('creates a vote for an anonymous guest and starts a guest session', function () {
    $article = ResourceHubArticle::factory()->public()->create();

    $response = postJson(route('portals.resource-hub.api.article-vote.store'), [
        'article_vote' => true,
        'article_id' => $article->getKey(),
    ]);

    $response->assertOk()
        ->assertJson([
            'is_helpful' => true,
            'helpful_vote_percentage' => 100,
        ]);

    expect(PortalGuest::count())->toBe(1);

    assertDatabaseHas(ResourceHubArticleVote::class, [
        'article_id' => $article->getKey(),
        'is_helpful' => true,
        'voter_type' => (new PortalGuest())->getMorphClass(),
    ]);
});

it('creates a vote for an authenticated student', function () {
    $student = Student::factory()->create();

    $article = ResourceHubArticle::factory()->public()->create();

    $response = actingAs($student, 'student')
        ->postJson(route('portals.resource-hub.api.article-vote.store'), [
            'article_vote' => false,
            'article_id' => $article->getKey(),
        ]);

    $response->assertOk()
        ->assertJson([
            'is_helpful' => false,
            'helpful_vote_percentage' => 0,
        ]);

    assertDatabaseHas(ResourceHubArticleVote::class, [
        'article_id' => $article->getKey(),
        'voter_id' => $student->getKey(),
        'voter_type' => $student->getMorphClass(),
        'is_helpful' => false,
    ]);
});

it('creates a vote for an authenticated prospect', function () {
    $prospect = Prospect::factory()->create();

    $article = ResourceHubArticle::factory()->public()->create();

    $response = actingAs($prospect, 'prospect')
        ->postJson(route('portals.resource-hub.api.article-vote.store'), [
            'article_vote' => true,
            'article_id' => $article->getKey(),
        ]);

    $response->assertOk()
        ->assertJson(['is_helpful' => true]);

    assertDatabaseHas(ResourceHubArticleVote::class, [
        'article_id' => $article->getKey(),
        'voter_id' => $prospect->getKey(),
        'voter_type' => $prospect->getMorphClass(),
        'is_helpful' => true,
    ]);
});

it('switches an existing vote instead of creating a duplicate', function () {
    $student = Student::factory()->create();

    $article = ResourceHubArticle::factory()->public()->create();

    actingAs($student, 'student')->postJson(route('portals.resource-hub.api.article-vote.store'), [
        'article_vote' => true,
        'article_id' => $article->getKey(),
    ]);

    $response = actingAs($student, 'student')->postJson(route('portals.resource-hub.api.article-vote.store'), [
        'article_vote' => false,
        'article_id' => $article->getKey(),
    ]);

    $response->assertOk()->assertJson(['is_helpful' => false]);

    expect(ResourceHubArticleVote::where('article_id', $article->getKey())->count())->toBe(1);

    assertDatabaseHas(ResourceHubArticleVote::class, [
        'article_id' => $article->getKey(),
        'voter_id' => $student->getKey(),
        'is_helpful' => false,
    ]);
});

it('removes the vote when toggled off', function () {
    $student = Student::factory()->create();

    $article = ResourceHubArticle::factory()->public()->create();

    actingAs($student, 'student')->postJson(route('portals.resource-hub.api.article-vote.store'), [
        'article_vote' => true,
        'article_id' => $article->getKey(),
    ]);

    $response = actingAs($student, 'student')->postJson(route('portals.resource-hub.api.article-vote.store'), [
        'article_vote' => null,
        'article_id' => $article->getKey(),
    ]);

    $response->assertOk()
        ->assertJson([
            'is_helpful' => null,
            'helpful_vote_percentage' => 0,
        ]);

    assertDatabaseMissing(ResourceHubArticleVote::class, [
        'article_id' => $article->getKey(),
        'voter_id' => $student->getKey(),
    ]);
});

it('calculates the helpful vote percentage across all voters', function () {
    $article = ResourceHubArticle::factory()->public()->create();

    ResourceHubArticleVote::factory()->for($article, 'resourceHubArticle')->create(['is_helpful' => true]);
    ResourceHubArticleVote::factory()->for($article, 'resourceHubArticle')->create(['is_helpful' => true]);
    ResourceHubArticleVote::factory()->for($article, 'resourceHubArticle')->create(['is_helpful' => false]);

    $student = Student::factory()->create();

    $response = actingAs($student, 'student')->postJson(route('portals.resource-hub.api.article-vote.store'), [
        'article_vote' => false,
        'article_id' => $article->getKey(),
    ]);

    $response->assertOk()->assertJson(['helpful_vote_percentage' => 50]);
});

it('does not persist a vote while the feature flag is inactive', function () {
    ResourceHubArticleFeedbackFeature::deactivate();

    $article = ResourceHubArticle::factory()->public()->create();

    $response = postJson(route('portals.resource-hub.api.article-vote.store'), [
        'article_vote' => true,
        'article_id' => $article->getKey(),
    ]);

    $response->assertOk()
        ->assertJson([
            'is_helpful' => null,
            'helpful_vote_percentage' => 0,
        ]);

    expect(PortalGuest::count())->toBe(0)
        ->and(ResourceHubArticleVote::count())->toBe(0);
});
