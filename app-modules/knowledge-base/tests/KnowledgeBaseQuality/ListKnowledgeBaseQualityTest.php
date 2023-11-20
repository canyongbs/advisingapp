<?php

use App\Models\User;

use function Pest\Laravel\actingAs;

use Assist\KnowledgeBase\Filament\Resources\KnowledgeBaseQualityResource;

// TODO: Write ListKnowledgeBaseQuality tests
//test('The correct details are displayed on the ListKnowledgeBaseQuality page', function () {});

// TODO: Sorting and Searching tests

// Permission Tests

test('ListKnowledgeBaseQuality is gated with proper access control', function () {
    $user = User::factory()->create();

    actingAs($user)
        ->get(
            KnowledgeBaseQualityResource::getUrl('index')
        )->assertForbidden();

    $user->givePermissionTo('knowledge_base_quality.view-any');

    actingAs($user)
        ->get(
            KnowledgeBaseQualityResource::getUrl('index')
        )->assertSuccessful();
});
