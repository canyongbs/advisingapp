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

use AdvisingApp\Authorization\Enums\LicenseType;
use AdvisingApp\ResourceHub\Filament\Resources\ResourceHubArticles\Pages\EditResourceHubArticle;
use AdvisingApp\ResourceHub\Filament\Resources\ResourceHubArticles\ResourceHubArticleResource;
use AdvisingApp\ResourceHub\Models\ResourceHubArticle;
use AdvisingApp\ResourceHub\Tests\Tenant\ResourceHubArticle\RequestFactories\EditResourceHubArticleRequestFactory;
use App\Models\User;
use App\Settings\LicenseSettings;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;
use function Pest\Livewire\livewire;
use function Tests\asSuperAdmin;

// TODO: Write EditResourceHubArticle tests
//test('A successful action on the EditResourceHubArticle page', function () {});
//
//test('EditResourceHubArticle requires valid data', function ($data, $errors) {})->with([]);

// Permission Tests

test('EditResourceHubArticle is gated with proper access control', function () {
    $user = User::factory()->licensed(LicenseType::cases())->create();

    actingAs($user);

    $resourceHubArticle = ResourceHubArticle::factory()->create();

    get(
        ResourceHubArticleResource::getUrl('edit', [
            'record' => $resourceHubArticle,
        ])
    )->assertForbidden();

    livewire(EditResourceHubArticle::class, [
        'record' => $resourceHubArticle->getRouteKey(),
    ])
        ->assertForbidden();

    $user->givePermissionTo('resource_hub_article.view-any');
    $user->givePermissionTo('resource_hub_article.*.update');

    get(
        ResourceHubArticleResource::getUrl('edit', [
            'record' => $resourceHubArticle,
        ])
    )->assertSuccessful();

    // TODO Restore testing the edit form
});

test('EditResourceHubArticle is gated with proper feature access control', function () {
    $settings = app(LicenseSettings::class);

    $settings->data->addons->resourceHub = false;

    $settings->save();

    $user = User::factory()->licensed(LicenseType::cases())->create();

    actingAs($user);

    $user->givePermissionTo('resource_hub_article.view-any');
    $user->givePermissionTo('resource_hub_article.*.update');

    $resourceHubArticle = ResourceHubArticle::factory()->create();

    get(
        ResourceHubArticleResource::getUrl('edit', [
            'record' => $resourceHubArticle,
        ])
    )->assertForbidden();

    livewire(EditResourceHubArticle::class, [
        'record' => $resourceHubArticle->getRouteKey(),
    ])
        ->assertForbidden();

    $settings->data->addons->resourceHub = true;

    $settings->save();

    get(
        ResourceHubArticleResource::getUrl('edit', [
            'record' => $resourceHubArticle,
        ])
    )->assertSuccessful();

    // TODO Restore testing the edit form
});

test('EditResourceHubArticle does not allow for duplicate article titles of non-deleted articles case insensitively', function () {
    asSuperAdmin();

    $deletedArticle = ResourceHubArticle::factory(['title' => 'Article Title'])->create();
    $article = ResourceHubArticle::factory(['title' => 'Test Title'])->create();
    ResourceHubArticle::factory(['title' => 'Other Title'])->create();
    $request1 = collect(EditResourceHubArticleRequestFactory::new(['title' => 'article title'])->create());
    $request2 = collect(EditResourceHubArticleRequestFactory::new(['title' => 'OTHER title'])->create());

    $deletedArticle->delete();

    livewire(EditResourceHubArticle::class, ['record' => $article->getRouteKey()])
        ->fillForm($request1->toArray())
        ->call('save')
        ->assertHasNoFormErrors();
    
    livewire(EditResourceHubArticle::class, ['record' => $article->getRouteKey()])
        ->fillForm($request2->toArray())
        ->call('save')
        ->assertHasFormErrors(['title' => 'unique']);
});
