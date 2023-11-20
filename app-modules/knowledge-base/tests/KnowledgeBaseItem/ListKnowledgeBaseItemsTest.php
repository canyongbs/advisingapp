<?php

use App\Models\User;

use function Pest\Laravel\actingAs;

use Assist\KnowledgeBase\Filament\Resources\KnowledgeBaseItemResource;

// TODO: Write ListKnowledgeBaseItems tests
//test('The correct details are displayed on the ListKnowledgeBaseItems page', function () {});

// TODO: Sorting and Searching tests

// Permission Tests

test('ListKnowledgeBaseItems is gated with proper access control', function () {
    $user = User::factory()->create();

    actingAs($user)
        ->get(
            KnowledgeBaseItemResource::getUrl('index')
        )->assertForbidden();

    $user->givePermissionTo('knowledge_base_item.view-any');

    actingAs($user)
        ->get(
            KnowledgeBaseItemResource::getUrl('index')
        )->assertSuccessful();
});
