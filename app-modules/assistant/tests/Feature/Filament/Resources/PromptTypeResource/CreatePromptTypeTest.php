<?php

use function Pest\Laravel\get;
use function Pest\Laravel\actingAs;
use function Pest\Livewire\livewire;

use AdvisingApp\Assistant\Models\PromptType;

use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\assertDatabaseCount;

use AdvisingApp\Authorization\Enums\LicenseType;
use AdvisingApp\Assistant\Filament\Resources\PromptTypeResource;
use AdvisingApp\Assistant\Filament\Resources\PromptTypeResource\Pages\CreatePromptType;

it('cannot render without a license', function () {
    actingAs(user([
        'prompt_type.view-any',
        'prompt_type.create',
    ]));

    get(PromptTypeResource::getUrl('create'))
        ->assertForbidden();
});

it('cannot render without permissions', function () {
    actingAs(user(licenses: LicenseType::ConversationalAi));

    get(PromptTypeResource::getUrl('create'))
        ->assertForbidden();
});

it('can render', function () {
    actingAs(user([
        'prompt_type.view-any',
        'prompt_type.create',
    ], LicenseType::ConversationalAi));

    get(PromptTypeResource::getUrl('create'))
        ->assertSuccessful();
});

it('can create a record', function () {
    actingAs(user([
        'prompt_type.view-any',
        'prompt_type.create',
    ], LicenseType::ConversationalAi));

    $record = PromptType::factory()->make();

    assertDatabaseCount(PromptType::class, 0);

    livewire(CreatePromptType::class)
        ->assertSuccessful()
        ->fillForm($record->toArray())
        ->call('create')
        ->assertHasNoFormErrors();

    assertDatabaseCount(PromptType::class, 1);

    assertDatabaseHas(PromptType::class, $record->toArray());
});
