<?php

use App\Models\User;

use function Pest\Laravel\actingAs;

use Assist\KnowledgeBase\Models\KnowledgeBaseCategory;
use Assist\KnowledgeBase\Filament\Resources\KnowledgeBaseCategoryResource;

// TODO: Write ViewKnowledgeBaseCategory tests
//test('The correct details are displayed on the ViewKnowledgeBaseCategory page', function () {});

// Permission Tests

test('ViewKnowledgeBaseCategory is gated with proper access control', function () {
    $user = User::factory()->create();

    $knowledgeBaseCategory = KnowledgeBaseCategory::factory()->create();

    actingAs($user)
        ->get(
            KnowledgeBaseCategoryResource::getUrl('view', [
                'record' => $knowledgeBaseCategory,
            ])
        )->assertForbidden();

    $user->givePermissionTo('knowledge_base_category.view-any');
    $user->givePermissionTo('knowledge_base_category.*.view');

    actingAs($user)
        ->get(
            KnowledgeBaseCategoryResource::getUrl('view', [
                'record' => $knowledgeBaseCategory,
            ])
        )->assertSuccessful();
});
