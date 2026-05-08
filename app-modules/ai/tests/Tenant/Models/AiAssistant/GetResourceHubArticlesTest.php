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
      of the licensor in the software. Any use of the licensor's trademarks is subject
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

use AdvisingApp\Ai\Enums\AiAssistantResourceHubArticleAccess;
use AdvisingApp\Ai\Models\AiAssistant;
use AdvisingApp\ResourceHub\Models\ResourceHubArticle;
use AdvisingApp\ResourceHub\Models\ResourceHubCategory;
use Illuminate\Support\Facades\Queue;

it('returns no articles when has_resource_hub_knowledge is false', function () {
    Queue::fake();

    ResourceHubArticle::factory()->count(3)->create([
        'public' => true,
    ]);

    $assistant = AiAssistant::factory()->create([
        'has_resource_hub_knowledge' => false,
        'resource_hub_article_access' => AiAssistantResourceHubArticleAccess::All,
    ]);

    expect($assistant->getResourceHubArticles())->toBeEmpty();
});

it('returns all articles when access is All', function () {
    Queue::fake();

    $publicArticles = ResourceHubArticle::factory()->count(2)->create([
        'public' => true,
    ]);

    $internalArticles = ResourceHubArticle::factory()->count(2)->create([
        'public' => false,
    ]);

    $assistant = AiAssistant::factory()->create([
        'has_resource_hub_knowledge' => true,
        'resource_hub_article_access' => AiAssistantResourceHubArticleAccess::All,
    ]);

    $articleIds = collect($assistant->getResourceHubArticles())->pluck('id');

    expect($articleIds)->toHaveCount(4);
    expect($articleIds->toArray())->toEqualCanonicalizing(
        $publicArticles->merge($internalArticles)->pluck('id')->toArray()
    );
});

it('returns only public articles when access is Public', function () {
    Queue::fake();

    $publicArticles = ResourceHubArticle::factory()->count(2)->create([
        'public' => true,
    ]);

    $internalArticles = ResourceHubArticle::factory()->count(2)->create([
        'public' => false,
    ]);

    $assistant = AiAssistant::factory()->create([
        'has_resource_hub_knowledge' => true,
        'resource_hub_article_access' => AiAssistantResourceHubArticleAccess::Public,
    ]);

    $articleIds = collect($assistant->getResourceHubArticles())->pluck('id');

    expect($articleIds)->toHaveCount(2);
    expect($articleIds->toArray())->toEqualCanonicalizing($publicArticles->pluck('id')->toArray());
    expect($articleIds->toArray())->not()->toEqualCanonicalizing($internalArticles->pluck('id')->toArray());
});

it('returns only internal articles when access is Internal', function () {
    Queue::fake();

    $publicArticles = ResourceHubArticle::factory()->count(2)->create([
        'public' => true,
    ]);

    $internalArticles = ResourceHubArticle::factory()->count(2)->create([
        'public' => false,
    ]);

    $assistant = AiAssistant::factory()->create([
        'has_resource_hub_knowledge' => true,
        'resource_hub_article_access' => AiAssistantResourceHubArticleAccess::Internal,
    ]);

    $articleIds = collect($assistant->getResourceHubArticles())->pluck('id');

    expect($articleIds)->toHaveCount(2);
    expect($articleIds->toArray())->toEqualCanonicalizing($internalArticles->pluck('id')->toArray());
    expect($articleIds->toArray())->not()->toEqualCanonicalizing($publicArticles->pluck('id')->toArray());
});

it('excludes articles without article_details', function () {
    Queue::fake();

    $articleWithDetails = ResourceHubArticle::factory()->create([
        'public' => true,
    ]);

    ResourceHubArticle::factory()->create([
        'public' => true,
        'article_details' => null,
    ]);

    $assistant = AiAssistant::factory()->create([
        'has_resource_hub_knowledge' => true,
        'resource_hub_article_access' => AiAssistantResourceHubArticleAccess::All,
    ]);

    $articleIds = collect($assistant->getResourceHubArticles())->pluck('id');

    expect($articleIds)->toHaveCount(1);
    expect($articleIds->first())->toBe($articleWithDetails->id);
});

it('filters articles by associated resource hub categories', function () {
    Queue::fake();

    $categoryA = ResourceHubCategory::factory()->create();
    $categoryB = ResourceHubCategory::factory()->create();

    $articlesInCategoryA = ResourceHubArticle::factory()->count(2)->create([
        'public' => true,
        'category_id' => $categoryA->id,
    ]);

    $articlesInCategoryB = ResourceHubArticle::factory()->count(2)->create([
        'public' => true,
        'category_id' => $categoryB->id,
    ]);

    $assistant = AiAssistant::factory()->create([
        'has_resource_hub_knowledge' => true,
        'resource_hub_article_access' => AiAssistantResourceHubArticleAccess::All,
    ]);

    $assistant->resourceHubCategories()->attach($categoryA);

    $articleIds = collect($assistant->getResourceHubArticles())->pluck('id');

    expect($articleIds)->toHaveCount(2);
    expect($articleIds->toArray())->toEqualCanonicalizing($articlesInCategoryA->pluck('id')->toArray());
    expect($articleIds->toArray())->not()->toEqualCanonicalizing($articlesInCategoryB->pluck('id')->toArray());
});

it('returns all matching articles when no categories are attached', function () {
    Queue::fake();

    $articles = ResourceHubArticle::factory()->count(3)->create([
        'public' => true,
    ]);

    $assistant = AiAssistant::factory()->create([
        'has_resource_hub_knowledge' => true,
        'resource_hub_article_access' => AiAssistantResourceHubArticleAccess::All,
    ]);

    $articleIds = collect($assistant->getResourceHubArticles())->pluck('id');

    expect($articleIds)->toHaveCount(3);
    expect($articleIds->toArray())->toEqualCanonicalizing($articles->pluck('id')->toArray());
});
