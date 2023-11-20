<?php

use App\Models\User;

use function Pest\Laravel\actingAs;

use Assist\KnowledgeBase\Models\KnowledgeBaseStatus;
use Assist\KnowledgeBase\Filament\Resources\KnowledgeBaseStatusResource;

// TODO: Write ViewKnowledgeBaseStatus tests
//test('The correct details are displayed on the ViewKnowledgeBaseStatus page', function () {});

// Permission Tests

test('ViewKnowledgeBaseStatus is gated with proper access control', function () {
    $user = User::factory()->create();

    $knowledgeBaseStatus = KnowledgeBaseStatus::factory()->create();

    actingAs($user)
        ->get(
            KnowledgeBaseStatusResource::getUrl('view', [
                'record' => $knowledgeBaseStatus,
            ])
        )->assertForbidden();

    $user->givePermissionTo('knowledge_base_status.view-any');
    $user->givePermissionTo('knowledge_base_status.*.view');

    actingAs($user)
        ->get(
            KnowledgeBaseStatusResource::getUrl('view', [
                'record' => $knowledgeBaseStatus,
            ])
        )->assertSuccessful();
});
