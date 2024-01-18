<?php

use function Pest\Laravel\get;
use function Pest\Laravel\actingAs;
use function Pest\Livewire\livewire;

use AdvisingApp\Assistant\Models\PromptType;

use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\assertDatabaseCount;

use AdvisingApp\Authorization\Enums\LicenseType;
use AdvisingApp\Assistant\Filament\Resources\PromptTypeResource;
use AdvisingApp\Assistant\Filament\Resources\PromptTypeResource\Pages\ViewPromptType;

it('cannot render without a license', function () {
    actingAs(user([
        'prompt_type.view-any',
        'prompt_type.*.view',
    ]));

    $record = PromptType::factory()->create();

    get(PromptTypeResource::getUrl('view', ['record' => $record]))
        ->assertForbidden();
});

it('cannot render without permissions', function () {
    actingAs(user(licenses: LicenseType::ConversationalAi));

    $record = PromptType::factory()->create();

    get(PromptTypeResource::getUrl('view', ['record' => $record]))
        ->assertForbidden();
});

it('can render', function () {
    actingAs(user([
        'prompt_type.view-any',
        'prompt_type.*.view',
    ], LicenseType::ConversationalAi));

    $record = PromptType::factory()->create();

    get(PromptTypeResource::getUrl('view', ['record' => $record]))
        ->assertSuccessful();
});

it('can view a record', function () {
    actingAs(user([
        'prompt_type.view-any',
        'prompt_type.*.view',
    ], LicenseType::ConversationalAi));

    assertDatabaseCount(PromptType::class, 0);

    $record = PromptType::factory()->create();

    assertDatabaseCount(PromptType::class, 1);

    assertDatabaseHas(PromptType::class, $record->toArray());

    livewire(ViewPromptType::class, [
        'record' => $record->getRouteKey(),
    ])
        ->assertSuccessful();

    assertDatabaseCount(PromptType::class, 1);

    assertDatabaseHas(PromptType::class, $record->toArray());
});
