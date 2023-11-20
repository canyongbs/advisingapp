<?php

use App\Models\User;

use function Pest\Laravel\actingAs;

use Assist\KnowledgeBase\Models\KnowledgeBaseItem;
use Assist\KnowledgeBase\Filament\Resources\KnowledgeBaseItemResource;

// TODO: Write ViewKnowledgeBaseItem tests
//test('The correct details are displayed on the ViewKnowledgeBaseItem page', function () {});

// Permission Tests

test('ViewKnowledgeBaseItem is gated with proper access control', function () {
    $user = User::factory()->create();

    $knowledgeBaseItem = KnowledgeBaseItem::factory()->create();

    actingAs($user)
        ->get(
            KnowledgeBaseItemResource::getUrl('view', [
                'record' => $knowledgeBaseItem,
            ])
        )->assertForbidden();

    $user->givePermissionTo('knowledge_base_item.view-any');
    $user->givePermissionTo('knowledge_base_item.*.view');

    actingAs($user)
        ->get(
            KnowledgeBaseItemResource::getUrl('view', [
                'record' => $knowledgeBaseItem,
            ])
        )->assertSuccessful();
});
