<?php

use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Livewire\livewire;
use function PHPUnit\Framework\assertCount;
use function Pest\Laravel\assertDatabaseHas;

use Assist\KnowledgeBase\Models\KnowledgeBaseItem;
use Assist\KnowledgeBase\Filament\Resources\KnowledgeBaseItemResource;
use Assist\KnowledgeBase\Tests\KnowledgeBaseItem\RequestFactories\CreateKnowledgeBaseItemRequestFactory;

// TODO: Write CreateKnowledgeBaseItem tests
//test('A successful action on the CreateKnowledgeBaseItem page', function () {});
//
//test('CreateKnowledgeBaseItem requires valid data', function ($data, $errors) {})->with([]);

// Permission Tests

test('CreateKnowledgeBaseItem is gated with proper access control', function () {
    $user = User::factory()->create();

    actingAs($user)
        ->get(
            KnowledgeBaseItemResource::getUrl('create')
        )->assertForbidden();

    livewire(KnowledgeBaseItemResource\Pages\CreateKnowledgeBaseItem::class)
        ->assertForbidden();

    $user->givePermissionTo('knowledge_base_item.view-any');
    $user->givePermissionTo('knowledge_base_item.create');

    actingAs($user)
        ->get(
            KnowledgeBaseItemResource::getUrl('create')
        )->assertSuccessful();

    $request = collect(CreateKnowledgeBaseItemRequestFactory::new()->create());

    livewire(KnowledgeBaseItemResource\Pages\CreateKnowledgeBaseItem::class)
        ->fillForm($request->toArray())
        ->call('create')
        ->assertHasNoFormErrors();

    assertCount(1, KnowledgeBaseItem::all());

    assertDatabaseHas(KnowledgeBaseItem::class, $request->except('institution')->toArray());

    $knowledgeBaseItem = KnowledgeBaseItem::first();

    expect($knowledgeBaseItem->institution->pluck('id')->toArray())->toEqual($request['institution']);
});
