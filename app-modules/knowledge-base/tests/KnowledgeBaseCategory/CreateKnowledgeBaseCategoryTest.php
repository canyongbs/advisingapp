<?php

use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Livewire\livewire;
use function PHPUnit\Framework\assertCount;
use function Pest\Laravel\assertDatabaseHas;

use Assist\KnowledgeBase\Models\KnowledgeBaseCategory;
use Assist\KnowledgeBase\Filament\Resources\KnowledgeBaseCategoryResource;
use Assist\KnowledgeBase\Tests\KnowledgeBaseCategory\RequestFactories\CreateKnowledgeBaseCategoryRequestFactory;

// TODO: Write CreateKnowledgeBaseCategory tests
//test('A successful action on the CreateKnowledgeBaseCategory page', function () {});
//
//test('CreateKnowledgeBaseCategory requires valid data', function ($data, $errors) {})->with([]);

// Permission Tests

test('CreateKnowledgeBaseCategory is gated with proper access control', function () {
    $user = User::factory()->create();

    actingAs($user)
        ->get(
            KnowledgeBaseCategoryResource::getUrl('create')
        )->assertForbidden();

    livewire(KnowledgeBaseCategoryResource\Pages\CreateKnowledgeBaseCategory::class)
        ->assertForbidden();

    $user->givePermissionTo('knowledge_base_category.view-any');
    $user->givePermissionTo('knowledge_base_category.create');

    actingAs($user)
        ->get(
            KnowledgeBaseCategoryResource::getUrl('create')
        )->assertSuccessful();

    $request = collect(CreateKnowledgeBaseCategoryRequestFactory::new()->create());

    livewire(KnowledgeBaseCategoryResource\Pages\CreateKnowledgeBaseCategory::class)
        ->fillForm($request->toArray())
        ->call('create')
        ->assertHasNoFormErrors();

    assertCount(1, KnowledgeBaseCategory::all());

    assertDatabaseHas(KnowledgeBaseCategory::class, $request->toArray());
});
