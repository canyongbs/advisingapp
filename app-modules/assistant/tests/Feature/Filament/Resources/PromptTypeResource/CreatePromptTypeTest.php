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

/** @var array<LicenseType> $licenses */
$licenses = [
    LicenseType::ConversationalAi,
];

$roles = [
    'assistant.assistant_prompt_management',
];

it('cannot render without a license', function () use ($roles) {
    actingAs(user(
        roles: $roles
    ));

    get(PromptTypeResource::getUrl('create'))
        ->assertForbidden();
});

it('cannot render without permissions', function () use ($licenses) {
    actingAs(user(
        licenses: $licenses,
    ));

    get(PromptTypeResource::getUrl('create'))
        ->assertForbidden();
});

it('can render', function () use ($licenses, $roles) {
    actingAs(user(
        licenses: $licenses,
        roles: $roles
    ));

    get(PromptTypeResource::getUrl('create'))
        ->assertSuccessful();
});

it('can create a record', function () use ($roles, $licenses) {
    actingAs(user(
        licenses: $licenses,
        roles: $roles
    ));

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
