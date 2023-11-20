<?php

use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Livewire\livewire;
use function PHPUnit\Framework\assertEquals;

use Assist\KnowledgeBase\Models\KnowledgeBaseStatus;
use Assist\KnowledgeBase\Filament\Resources\KnowledgeBaseStatusResource;
use Assist\KnowledgeBase\Tests\KnowledgeBaseStatus\RequestFactories\EditKnowledgeBaseStatusRequestFactory;

// TODO: Write EditKnowledgeBaseStatus tests
//test('A successful action on the EditKnowledgeBaseStatus page', function () {});
//
//test('EditKnowledgeBaseStatus requires valid data', function ($data, $errors) {})->with([]);

// Permission Tests

test('EditKnowledgeBaseStatus is gated with proper access control', function () {
    $user = User::factory()->create();

    $knowledgeBaseStatus = KnowledgeBaseStatus::factory()->create();

    actingAs($user)
        ->get(
            KnowledgeBaseStatusResource::getUrl('edit', [
                'record' => $knowledgeBaseStatus,
            ])
        )->assertForbidden();

    livewire(KnowledgeBaseStatusResource\Pages\EditKnowledgeBaseStatus::class, [
        'record' => $knowledgeBaseStatus->getRouteKey(),
    ])
        ->assertForbidden();

    $user->givePermissionTo('knowledge_base_status.view-any');
    $user->givePermissionTo('knowledge_base_status.*.update');

    actingAs($user)
        ->get(
            KnowledgeBaseStatusResource::getUrl('edit', [
                'record' => $knowledgeBaseStatus,
            ])
        )->assertSuccessful();

    $request = collect(EditKnowledgeBaseStatusRequestFactory::new()->create());

    livewire(KnowledgeBaseStatusResource\Pages\EditKnowledgeBaseStatus::class, [
        'record' => $knowledgeBaseStatus->getRouteKey(),
    ])
        ->fillForm($request->toArray())
        ->call('save')
        ->assertHasNoFormErrors();

    assertEquals($request['name'], $knowledgeBaseStatus->fresh()->name);
});
