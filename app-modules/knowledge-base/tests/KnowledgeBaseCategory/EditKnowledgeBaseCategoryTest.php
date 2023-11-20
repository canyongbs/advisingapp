<?php

use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Livewire\livewire;
use function PHPUnit\Framework\assertEquals;

use Assist\KnowledgeBase\Models\KnowledgeBaseCategory;
use Assist\KnowledgeBase\Filament\Resources\KnowledgeBaseCategoryResource;
use Assist\KnowledgeBase\Tests\KnowledgeBaseCategory\RequestFactories\EditKnowledgeBaseCategoryRequestFactory;

// TODO: Write EditKnowledgeBaseCategory tests
//test('A successful action on the EditKnowledgeBaseCategory page', function () {});
//
//test('EditKnowledgeBaseCategory requires valid data', function ($data, $errors) {})->with([]);

// Permission Tests

test('EditKnowledgeBaseCategory is gated with proper access control', function () {
    $user = User::factory()->create();

    $knowledgeBaseCategory = KnowledgeBaseCategory::factory()->create();

    actingAs($user)
        ->get(
            KnowledgeBaseCategoryResource::getUrl('edit', [
                'record' => $knowledgeBaseCategory,
            ])
        )->assertForbidden();

    livewire(KnowledgeBaseCategoryResource\Pages\EditKnowledgeBaseCategory::class, [
        'record' => $knowledgeBaseCategory->getRouteKey(),
    ])
        ->assertForbidden();

    $user->givePermissionTo('knowledge_base_category.view-any');
    $user->givePermissionTo('knowledge_base_category.*.update');

    actingAs($user)
        ->get(
            KnowledgeBaseCategoryResource::getUrl('edit', [
                'record' => $knowledgeBaseCategory,
            ])
        )->assertSuccessful();

    $request = collect(EditKnowledgeBaseCategoryRequestFactory::new()->create());

    livewire(KnowledgeBaseCategoryResource\Pages\EditKnowledgeBaseCategory::class, [
        'record' => $knowledgeBaseCategory->getRouteKey(),
    ])
        ->fillForm($request->toArray())
        ->call('save')
        ->assertHasNoFormErrors();

    assertEquals($request['name'], $knowledgeBaseCategory->fresh()->name);
});
