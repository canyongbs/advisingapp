<?php

use App\Models\User;

use function Pest\Laravel\actingAs;

use Assist\KnowledgeBase\Filament\Resources\KnowledgeBaseCategoryResource;

// TODO: Write ListKnowledgeBaseCategory tests
//test('The correct details are displayed on the ListKnowledgeBaseCategory page', function () {});

// TODO: Sorting and Searching tests

// Permission Tests

test('ListKnowledgeBaseCategory is gated with proper access control', function () {
    $user = User::factory()->create();

    actingAs($user)
        ->get(
            KnowledgeBaseCategoryResource::getUrl('index')
        )->assertForbidden();

    $user->givePermissionTo('knowledge_base_category.view-any');

    actingAs($user)
        ->get(
            KnowledgeBaseCategoryResource::getUrl('index')
        )->assertSuccessful();
});
