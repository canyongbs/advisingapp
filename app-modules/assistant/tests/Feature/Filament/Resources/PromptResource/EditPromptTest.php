<?php

use function Pest\Laravel\get;

use Filament\Actions\DeleteAction;

use function Pest\Laravel\actingAs;
use function Pest\Livewire\livewire;

use AdvisingApp\Assistant\Models\Prompt;

use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\assertModelMissing;
use function Pest\Laravel\assertDatabaseCount;

use AdvisingApp\Authorization\Enums\LicenseType;
use AdvisingApp\Assistant\Filament\Resources\PromptResource;
use AdvisingApp\Assistant\Filament\Resources\PromptResource\Pages\EditPrompt;

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

    get(PromptResource::getUrl('edit', [
        'record' => $record->getRouteKey(),
    ]))
        ->assertForbidden();
});

it('cannot render without permissions', function () use ($licenses) {
    actingAs(user(
        licenses: $licenses,
    ));

    $record = Prompt::factory()->create();

    get(PromptResource::getUrl('edit', [
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

    get(PromptResource::getUrl('edit', [
        'record' => $record->getRouteKey(),
    ]))
        ->assertSuccessful();
});

it('can edit a record', function () use ($licenses, $roles) {
    actingAs(user(
        licenses: $licenses,
        roles: $roles
    ));

    $record = Prompt::factory()->make();

    livewire(EditPrompt::class, [
        'record' => Prompt::factory()->create()->getRouteKey(),
    ])
        ->assertSuccessful()
        ->fillForm($record->toArray())
        ->call('save')
        ->assertHasNoFormErrors();

    assertDatabaseCount(Prompt::class, 1);

    assertDatabaseHas(Prompt::class, $record->toArray());
});

it('can delete a record', function () use ($licenses, $roles) {
    actingAs(user(
        licenses: $licenses,
        roles: $roles
    ));

    $record = Prompt::factory()->create();

    assertDatabaseCount(Prompt::class, 1);

    assertDatabaseHas(Prompt::class, $record->toArray());

    livewire(EditPrompt::class, [
        'record' => $record->getRouteKey(),
    ])
        ->assertSuccessful()
        ->assertActionExists(DeleteAction::class)
        ->assertActionEnabled(DeleteAction::class)
        ->callAction(DeleteAction::class);

    assertDatabaseCount(Prompt::class, 0);

    assertModelMissing($record);
});
