<?php

use App\Models\User;

use function Pest\Laravel\actingAs;

use Assist\KnowledgeBase\Models\KnowledgeBaseQuality;
use Assist\KnowledgeBase\Filament\Resources\KnowledgeBaseQualityResource;

// TODO: Write ViewKnowledgeBaseQuality tests
//test('The correct details are displayed on the ViewKnowledgeBaseQuality page', function () {});

// Permission Tests

test('ViewKnowledgeBaseQuality is gated with proper access control', function () {
    $user = User::factory()->create();

    $knowledgeBaseQuality = KnowledgeBaseQuality::factory()->create();

    actingAs($user)
        ->get(
            KnowledgeBaseQualityResource::getUrl('view', [
                'record' => $knowledgeBaseQuality,
            ])
        )->assertForbidden();

    $user->givePermissionTo('knowledge_base_quality.view-any');
    $user->givePermissionTo('knowledge_base_quality.*.view');

    actingAs($user)
        ->get(
            KnowledgeBaseQualityResource::getUrl('view', [
                'record' => $knowledgeBaseQuality,
            ])
        )->assertSuccessful();
});
