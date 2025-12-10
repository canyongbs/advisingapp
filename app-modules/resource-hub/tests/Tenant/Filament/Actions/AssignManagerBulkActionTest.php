<?php

use AdvisingApp\ResourceHub\Filament\Actions\AssignManagerBulkAction;
use AdvisingApp\ResourceHub\Filament\Resources\ResourceHubArticles\Pages\ListResourceHubArticles;
use AdvisingApp\ResourceHub\Models\ResourceHubArticle;
use App\Models\User;
use Filament\Actions\Testing\TestAction;

use function Pest\Livewire\livewire;
use function Tests\asSuperAdmin;

it('can bulk assign managers to articles', function () {
    asSuperAdmin();

    $manager = User::factory()->create();

    $articles = ResourceHubArticle::factory(5)->create();

    livewire(ListResourceHubArticles::class)
        ->selectTableRecords($articles->pluck('id')->toArray())
        ->callAction(
            TestAction::make('bulkManagers')->table()->bulk(),
            [
                'manager_ids' => [$manager->id],
                'remove_prior' => false,
            ]
        )
        ->assertHasNoErrors();

    $articles->each(function(ResourceHubArticle $article) use ($manager) {
        expect($article->refresh()->managers->pluck('id'))->toContain($manager->id);
    });
});

it('can bulk assign managers to articles without removing previously assigned managers', function () {
    asSuperAdmin();

    $oldManager = User::factory()->create();
    $newManager = User::factory()->create();

    $articles = ResourceHubArticle::factory(5)->create();

    $articles->each(function (ResourceHubArticle $article) use ($oldManager) {
        $article->managers()->attach($oldManager->id);
    });

    livewire(ListResourceHubArticles::class)
        ->selectTableRecords($articles->pluck('id')->toArray())
        ->callAction(
            TestAction::make('bulkManagers')->table()->bulk(),
            [
                'manager_ids' => [$newManager->id],
                'remove_prior' => false,
            ]
            )
        ->assertHasNoErrors();

    $articles->each(function(ResourceHubArticle $article) use ($oldManager, $newManager) {
        expect($article->refresh()->managers->pluck('id'))->toContain($oldManager->id, $newManager->id);
    });
});

it('can bulk assign managers to articles with removing previously assigned managers', function () {
    asSuperAdmin();

    $oldManager = User::factory()->create();
    $newManager = User::factory()->create();

    $articles = ResourceHubArticle::factory(5)->create();

    $articles->each(function (ResourceHubArticle $article) use ($oldManager) {
        $article->managers()->attach($oldManager->id);
    });

    livewire(ListResourceHubArticles::class)
        ->selectTableRecords($articles->pluck('id')->toArray())
        ->callAction(
            TestAction::make('bulkManagers')->table()->bulk(),
            [
                'manager_ids' => [$newManager->id],
                'remove_prior' => true,
            ]
            )
        ->assertHasNoErrors();

    $articles->each(function(ResourceHubArticle $article) use ($oldManager, $newManager) {
        expect($article->refresh()->managers->pluck('id'))->toContain($newManager->id);
        expect($article->managers->pluck('id'))->not()->toContain($oldManager->id);
    });
});