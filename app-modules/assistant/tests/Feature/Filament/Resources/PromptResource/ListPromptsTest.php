<?php

use function Pest\Laravel\get;
use function Pest\Laravel\actingAs;
use function Pest\Livewire\livewire;

use AdvisingApp\Assistant\Models\Prompt;

use function Pest\Laravel\assertDatabaseCount;

use AdvisingApp\Authorization\Enums\LicenseType;
use AdvisingApp\Assistant\Filament\Resources\PromptResource;
use AdvisingApp\Assistant\Filament\Resources\PromptResource\Pages\ListPrompts;

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

    get(PromptResource::getUrl())
        ->assertForbidden();
});

it('cannot render without permissions', function () use ($licenses) {
    actingAs(user(
        licenses: $licenses,
    ));

    get(PromptResource::getUrl())
        ->assertForbidden();
});

it('can render', function () use ($licenses, $roles) {
    actingAs(user(
        licenses: $licenses,
        roles: $roles
    ));

    get(PromptResource::getUrl())
        ->assertSuccessful();
});

it('can list records', function () use ($licenses, $roles) {
    actingAs(user(
        licenses: $licenses,
        roles: $roles
    ));

    assertDatabaseCount(Prompt::class, 0);

    $records = Prompt::factory()->count(10)->create();

    assertDatabaseCount(Prompt::class, $records->count());

    livewire(ListPrompts::class)
        ->assertSuccessful()
        ->assertCountTableRecords($records->count())
        ->assertCanSeeTableRecords($records);
});
