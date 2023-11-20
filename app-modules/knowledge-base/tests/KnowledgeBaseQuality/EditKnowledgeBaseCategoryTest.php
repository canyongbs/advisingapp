<?php

use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Livewire\livewire;
use function PHPUnit\Framework\assertEquals;

use Assist\KnowledgeBase\Models\KnowledgeBaseQuality;
use Assist\KnowledgeBase\Filament\Resources\KnowledgeBaseQualityResource;
use Assist\KnowledgeBase\Tests\KnowledgeBaseQuality\RequestFactories\EditKnowledgeBaseQualityRequestFactory;

// TODO: Write EditKnowledgeBaseQuality tests
//test('A successful action on the EditKnowledgeBaseQuality page', function () {});
//
//test('EditKnowledgeBaseQuality requires valid data', function ($data, $errors) {})->with([]);

// Permission Tests

test('EditKnowledgeBaseQuality is gated with proper access control', function () {
    $user = User::factory()->create();

    $knowledgeBaseQuality = KnowledgeBaseQuality::factory()->create();

    actingAs($user)
        ->get(
            KnowledgeBaseQualityResource::getUrl('edit', [
                'record' => $knowledgeBaseQuality,
            ])
        )->assertForbidden();

    livewire(KnowledgeBaseQualityResource\Pages\EditKnowledgeBaseQuality::class, [
        'record' => $knowledgeBaseQuality->getRouteKey(),
    ])
        ->assertForbidden();

    $user->givePermissionTo('knowledge_base_quality.view-any');
    $user->givePermissionTo('knowledge_base_quality.*.update');

    actingAs($user)
        ->get(
            KnowledgeBaseQualityResource::getUrl('edit', [
                'record' => $knowledgeBaseQuality,
            ])
        )->assertSuccessful();

    $request = collect(EditKnowledgeBaseQualityRequestFactory::new()->create());

    livewire(KnowledgeBaseQualityResource\Pages\EditKnowledgeBaseQuality::class, [
        'record' => $knowledgeBaseQuality->getRouteKey(),
    ])
        ->fillForm($request->toArray())
        ->call('save')
        ->assertHasNoFormErrors();

    assertEquals($request['name'], $knowledgeBaseQuality->fresh()->name);
});
