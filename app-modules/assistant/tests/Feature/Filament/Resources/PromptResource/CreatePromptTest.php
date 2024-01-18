<?php

use function Pest\Laravel\get;
use function Pest\Laravel\actingAs;
use function Pest\Livewire\livewire;

use AdvisingApp\Assistant\Models\Prompt;

use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\assertDatabaseCount;

use AdvisingApp\Authorization\Enums\LicenseType;
use AdvisingApp\Assistant\Filament\Resources\PromptResource;
use AdvisingApp\Assistant\Filament\Resources\PromptResource\Pages\CreatePrompt;

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

    get(PromptResource::getUrl('create'))
        ->assertForbidden();
});

it('cannot render without permissions', function () use ($licenses) {
    actingAs(user(
        licenses: $licenses
    ));

    get(PromptResource::getUrl('create'))
        ->assertForbidden();
});

it('can render', function () use ($licenses, $roles) {
    actingAs(user(
        licenses: $licenses,
        roles: $roles
    ));

    get(PromptResource::getUrl('create'))
        ->assertSuccessful();
});

it('can create a record', function () use ($licenses, $roles) {
    actingAs(user(
        licenses: $licenses,
        roles: $roles
    ));

    $record = Prompt::factory()->make();

    assertDatabaseCount(Prompt::class, 0);

    livewire(CreatePrompt::class)
        ->assertSuccessful()
        ->fillForm($record->toArray())
        ->call('create')
        ->assertHasNoFormErrors();

    assertDatabaseCount(Prompt::class, 1);

    assertDatabaseHas(Prompt::class, $record->toArray());
});
