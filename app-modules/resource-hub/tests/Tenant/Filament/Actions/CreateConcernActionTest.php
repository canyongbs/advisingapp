<?php

use AdvisingApp\ResourceHub\Filament\Resources\ResourceHubArticles\Pages\ViewResourceHubArticle;
use AdvisingApp\ResourceHub\Models\ResourceHubArticle;
use AdvisingApp\ResourceHub\Tests\Tenant\Filament\Actions\RequestFactories\CreateConcernActionRequestFactory;

use function Pest\Livewire\livewire;
use function Tests\asSuperAdmin;

it('can create a concern properly', function () {
    asSuperAdmin();

    $resourceHubArticle = ResourceHubArticle::factory()->create();

    $data = CreateConcernActionRequestFactory::new()->create();

    livewire(ViewResourceHubArticle::class, ['record' => $resourceHubArticle->getKey()])
        ->callAction('raiseConcern', [
            'description' => $data['description'],
        ]);

    $resourceHubArticle->refresh();

    expect($resourceHubArticle->concerns()->count())->toBe(1);
    expect($resourceHubArticle->concerns()->first()->description)->toBe($data['description']);
});
