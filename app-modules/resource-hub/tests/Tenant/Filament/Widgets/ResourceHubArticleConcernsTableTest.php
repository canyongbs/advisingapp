<?php

use AdvisingApp\ResourceHub\Enums\ConcernStatus;
use AdvisingApp\ResourceHub\Filament\Widgets\ResourceHubArticleConcernsTable;
use AdvisingApp\ResourceHub\Models\ResourceHubArticle;
use AdvisingApp\ResourceHub\Models\ResourceHubArticleConcern;

use function Pest\Livewire\livewire;

it('returns all new concerns for a given resource hub article by default', function () {
    $resourceHubArticle = ResourceHubArticle::factory()->create();

    $newConcerns = ResourceHubArticleConcern::factory()
        ->count(3)
        ->state(['status' => ConcernStatus::New])
        ->for($resourceHubArticle, 'resourceHubArticle')
        ->create();
    
    $archivedConcerns = ResourceHubArticleConcern::factory()
        ->count(3)
        ->state(['status' => ConcernStatus::Archived])
        ->for($resourceHubArticle, 'resourceHubArticle')
        ->create();
    
    $resolvedConcerns = ResourceHubArticleConcern::factory()
        ->count(3)
        ->state(['status' => ConcernStatus::Resolved])
        ->for($resourceHubArticle, 'resourceHubArticle')
        ->create();

    livewire(ResourceHubArticleConcernsTable::class, ['resourceHubArticleId' => $resourceHubArticle->getKey()])
        ->assertCanSeeTableRecords($newConcerns)
        ->assertCanNotSeeTableRecords($archivedConcerns->merge($resolvedConcerns));
});

it('can filter concerns by status', function () {
    $resourceHubArticle = ResourceHubArticle::factory()->create();

    $newConcerns = ResourceHubArticleConcern::factory()
        ->count(3)
        ->state(['status' => ConcernStatus::New])
        ->for($resourceHubArticle, 'resourceHubArticle')
        ->create();
    
    $archivedConcerns = ResourceHubArticleConcern::factory()
        ->count(3)
        ->state(['status' => ConcernStatus::Archived])
        ->for($resourceHubArticle, 'resourceHubArticle')
        ->create();
    
    $resolvedConcerns = ResourceHubArticleConcern::factory()
        ->count(3)
        ->state(['status' => ConcernStatus::Resolved])
        ->for($resourceHubArticle, 'resourceHubArticle')
        ->create();

    livewire(ResourceHubArticleConcernsTable::class, ['resourceHubArticleId' => $resourceHubArticle->getKey()])
        ->filterTable('status', ConcernStatus::Archived->value)
        ->assertCanSeeTableRecords($archivedConcerns)
        ->assertCanNotSeeTableRecords($newConcerns->merge($resolvedConcerns))
        ->filterTable('status', ConcernStatus::Resolved->value)
        ->assertCanSeeTableRecords($resolvedConcerns)
        ->assertCanNotSeeTableRecords($newConcerns->merge($archivedConcerns));
});