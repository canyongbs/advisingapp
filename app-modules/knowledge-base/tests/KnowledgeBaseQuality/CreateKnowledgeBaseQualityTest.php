<?php

use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Livewire\livewire;
use function PHPUnit\Framework\assertCount;
use function Pest\Laravel\assertDatabaseHas;

use Assist\KnowledgeBase\Models\KnowledgeBaseQuality;
use Assist\KnowledgeBase\Filament\Resources\KnowledgeBaseQualityResource;
use Assist\KnowledgeBase\Tests\KnowledgeBaseQuality\RequestFactories\CreateKnowledgeBaseQualityRequestFactory;

// TODO: Write CreateKnowledgeBaseQuality tests
//test('A successful action on the CreateKnowledgeBaseQuality page', function () {});
//
//test('CreateKnowledgeBaseQuality requires valid data', function ($data, $errors) {})->with([]);

// Permission Tests

test('CreateKnowledgeBaseQuality is gated with proper access control', function () {
    $user = User::factory()->create();

    actingAs($user)
        ->get(
            KnowledgeBaseQualityResource::getUrl('create')
        )->assertForbidden();

    livewire(KnowledgeBaseQualityResource\Pages\CreateKnowledgeBaseQuality::class)
        ->assertForbidden();

    $user->givePermissionTo('knowledge_base_quality.view-any');
    $user->givePermissionTo('knowledge_base_quality.create');

    actingAs($user)
        ->get(
            KnowledgeBaseQualityResource::getUrl('create')
        )->assertSuccessful();

    $request = collect(CreateKnowledgeBaseQualityRequestFactory::new()->create());

    livewire(KnowledgeBaseQualityResource\Pages\CreateKnowledgeBaseQuality::class)
        ->fillForm($request->toArray())
        ->call('create')
        ->assertHasNoFormErrors();

    assertCount(1, KnowledgeBaseQuality::all());

    assertDatabaseHas(KnowledgeBaseQuality::class, $request->toArray());
});
