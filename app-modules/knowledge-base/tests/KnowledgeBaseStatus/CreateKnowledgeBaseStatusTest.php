<?php

use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Livewire\livewire;
use function PHPUnit\Framework\assertCount;
use function Pest\Laravel\assertDatabaseHas;

use Assist\KnowledgeBase\Models\KnowledgeBaseStatus;
use Assist\KnowledgeBase\Filament\Resources\KnowledgeBaseStatusResource;
use Assist\KnowledgeBase\Tests\KnowledgeBaseStatus\RequestFactories\CreateKnowledgeBaseStatusRequestFactory;

// TODO: Write CreateKnowledgeBaseStatus tests
//test('A successful action on the CreateKnowledgeBaseStatus page', function () {});
//
//test('CreateKnowledgeBaseStatus requires valid data', function ($data, $errors) {})->with([]);

// Permission Tests

test('CreateKnowledgeBaseStatus is gated with proper access control', function () {
    $user = User::factory()->create();

    actingAs($user)
        ->get(
            KnowledgeBaseStatusResource::getUrl('create')
        )->assertForbidden();

    livewire(KnowledgeBaseStatusResource\Pages\CreateKnowledgeBaseStatus::class)
        ->assertForbidden();

    $user->givePermissionTo('knowledge_base_status.view-any');
    $user->givePermissionTo('knowledge_base_status.create');

    actingAs($user)
        ->get(
            KnowledgeBaseStatusResource::getUrl('create')
        )->assertSuccessful();

    $request = collect(CreateKnowledgeBaseStatusRequestFactory::new()->create());

    livewire(KnowledgeBaseStatusResource\Pages\CreateKnowledgeBaseStatus::class)
        ->fillForm($request->toArray())
        ->call('create')
        ->assertHasNoFormErrors();

    assertCount(1, KnowledgeBaseStatus::all());

    assertDatabaseHas(KnowledgeBaseStatus::class, $request->toArray());
});
