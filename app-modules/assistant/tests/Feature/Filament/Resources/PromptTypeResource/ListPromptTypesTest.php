<?php

use function Pest\Laravel\get;
use function Pest\Laravel\actingAs;
use function Pest\Livewire\livewire;

use AdvisingApp\Assistant\Models\PromptType;

use function Pest\Laravel\assertDatabaseCount;

use AdvisingApp\Authorization\Enums\LicenseType;
use AdvisingApp\Assistant\Filament\Resources\PromptTypeResource;
use AdvisingApp\Assistant\Filament\Resources\PromptTypeResource\Pages\ListPromptTypes;

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

    get(PromptTypeResource::getUrl())
        ->assertForbidden();
});

it('cannot render without permissions', function () use ($licenses) {
    actingAs(user(
        licenses: $licenses,
    ));

    get(PromptTypeResource::getUrl())
        ->assertForbidden();
});

it('can render', function () use ($licenses, $roles) {
    actingAs(user(
        licenses: $licenses,
        roles: $roles
    ));

    get(PromptTypeResource::getUrl())
        ->assertSuccessful();
});

it('can list records', function () use ($licenses, $roles) {
    actingAs(user(
        licenses: $licenses,
        roles: $roles
    ));

    assertDatabaseCount(PromptType::class, 0);

    $records = PromptType::factory()->count(10)->create();

    assertDatabaseCount(PromptType::class, $records->count());

    livewire(ListPromptTypes::class)
        ->assertSuccessful()
        ->assertCountTableRecords($records->count())
        ->assertCanSeeTableRecords($records);
});
