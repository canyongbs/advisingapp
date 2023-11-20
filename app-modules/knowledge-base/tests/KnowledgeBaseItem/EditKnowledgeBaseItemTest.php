<?php

use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Livewire\livewire;

use Assist\KnowledgeBase\Models\KnowledgeBaseItem;
use Assist\KnowledgeBase\Filament\Resources\KnowledgeBaseItemResource;
use Assist\KnowledgeBase\Tests\KnowledgeBaseItem\RequestFactories\EditKnowledgeBaseItemRequestFactory;

// TODO: Write EditKnowledgeBaseItem tests
//test('A successful action on the EditKnowledgeBaseItem page', function () {});
//
//test('EditKnowledgeBaseItem requires valid data', function ($data, $errors) {})->with([]);

// Permission Tests

test('EditKnowledgeBaseItem is gated with proper access control', function () {
    $user = User::factory()->create();

    $knowledgeBaseItem = KnowledgeBaseItem::factory()->create();

    actingAs($user)
        ->get(
            KnowledgeBaseItemResource::getUrl('edit', [
                'record' => $knowledgeBaseItem,
            ])
        )->assertForbidden();

    livewire(KnowledgeBaseItemResource\Pages\EditKnowledgeBaseItem::class, [
        'record' => $knowledgeBaseItem->getRouteKey(),
    ])
        ->assertForbidden();

    $user->givePermissionTo('knowledge_base_item.view-any');
    $user->givePermissionTo('knowledge_base_item.*.update');

    actingAs($user)
        ->get(
            KnowledgeBaseItemResource::getUrl('edit', [
                'record' => $knowledgeBaseItem,
            ])
        )->assertSuccessful();

    $request = collect(EditKnowledgeBaseItemRequestFactory::new()->create());

    livewire(KnowledgeBaseItemResource\Pages\EditKnowledgeBaseItem::class, [
        'record' => $knowledgeBaseItem->getRouteKey(),
    ])
        ->fillForm($request->toArray())
        ->call('save')
        ->assertHasNoFormErrors();

    expect($knowledgeBaseItem->fresh()->only($request->except('division')->keys()->toArray()))
        ->toEqual($request->except('division')->toArray())
        ->and($knowledgeBaseItem->fresh()->division->pluck('id')->toArray())->toEqual($request['division']);
});
