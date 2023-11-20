<?php

use App\Models\User;

use function Pest\Laravel\actingAs;

use Assist\KnowledgeBase\Filament\Resources\KnowledgeBaseStatusResource;

// TODO: Write ListKnowledgeBaseStatuses tests
//test('The correct details are displayed on the ListKnowledgeBaseStatuses page', function () {});

// TODO: Sorting and Searching tests

// Permission Tests

test('ListKnowledgeBaseStatuses is gated with proper access control', function () {
    $user = User::factory()->create();

    actingAs($user)
        ->get(
            KnowledgeBaseStatusResource::getUrl('index')
        )->assertForbidden();

    $user->givePermissionTo('knowledge_base_status.view-any');

    actingAs($user)
        ->get(
            KnowledgeBaseStatusResource::getUrl('index')
        )->assertSuccessful();
});
