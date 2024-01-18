<?php

use function Pest\Laravel\get;
use function Pest\Laravel\actingAs;
use function Pest\Livewire\livewire;

use AdvisingApp\Assistant\Models\Prompt;

use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\assertDatabaseCount;

use AdvisingApp\Authorization\Enums\LicenseType;
use AdvisingApp\Assistant\Filament\Resources\PromptResource;
use AdvisingApp\Assistant\Filament\Resources\PromptResource\Pages\ViewPrompt;

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

    $record = Prompt::factory()->create();

    get(PromptResource::getUrl('view', [
        'record' => $record->getRouteKey(),
    ]))
        ->assertForbidden();
});

it('cannot render without permissions', function () use ($licenses) {
    actingAs(user(
        licenses: $licenses,
    ));

    $record = Prompt::factory()->create();

    get(PromptResource::getUrl('view', [
        'record' => $record->getRouteKey(),
    ]))
        ->assertForbidden();
});

it('can render', function () use ($licenses, $roles) {
    actingAs(user(
        licenses: $licenses,
        roles: $roles
    ));

    $record = Prompt::factory()->create();

    get(PromptResource::getUrl('view', [
        'record' => $record->getRouteKey(),
    ]))
        ->assertSuccessful();
});

it('can view a record', function () use ($licenses, $roles) {
    actingAs(user(
        licenses: $licenses,
        roles: $roles
    ));

    assertDatabaseCount(Prompt::class, 0);

    $record = Prompt::factory()->create();

    assertDatabaseCount(Prompt::class, 1);

    assertDatabaseHas(Prompt::class, $record->toArray());

    livewire(ViewPrompt::class, [
        'record' => $record->getRouteKey(),
    ])
        ->assertSuccessful();

    assertDatabaseCount(Prompt::class, 1);

    assertDatabaseHas(Prompt::class, $record->toArray());
});
