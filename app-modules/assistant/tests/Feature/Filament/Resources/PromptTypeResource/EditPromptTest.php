<?php

use function Pest\Laravel\get;

use Filament\Actions\DeleteAction;

use function Pest\Laravel\actingAs;
use function Pest\Livewire\livewire;

use AdvisingApp\Assistant\Models\PromptType;

use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\assertModelMissing;
use function Pest\Laravel\assertDatabaseCount;

use AdvisingApp\Authorization\Enums\LicenseType;
use AdvisingApp\Assistant\Filament\Resources\PromptTypeResource;
use AdvisingApp\Assistant\Filament\Resources\PromptTypeResource\Pages\EditPromptType;

it('cannot render without a license', function () {
    actingAs(user([
        'prompt_type.view-any',
        'prompt_type.*.update',
    ]));

    $record = PromptType::factory()->create();

    get(PromptTypeResource::getUrl('edit', ['record' => $record]))
        ->assertForbidden();
});

it('cannot render without permissions', function () {
    actingAs(user(licenses: LicenseType::ConversationalAi));

    $record = PromptType::factory()->create();

    get(PromptTypeResource::getUrl('edit', ['record' => $record]))
        ->assertForbidden();
});

it('can render', function () {
    actingAs(user([
        'prompt_type.view-any',
        'prompt_type.*.update',
    ], LicenseType::ConversationalAi));

    $record = PromptType::factory()->create();

    get(PromptTypeResource::getUrl('edit', ['record' => $record]))
        ->assertSuccessful();
});

it('can edit a record', function () {
    actingAs(user([
        'prompt_type.view-any',
        'prompt_type.*.update',
    ], LicenseType::ConversationalAi));

    $record = PromptType::factory()->make();

    livewire(EditPromptType::class, [
        'record' => PromptType::factory()->create()->getRouteKey(),
    ])
        ->assertSuccessful()
        ->fillForm($record->toArray())
        ->call('save')
        ->assertHasNoFormErrors();

    assertDatabaseCount(PromptType::class, 1);

    assertDatabaseHas(PromptType::class, $record->toArray());
});

it('can delete a record', function () {
    actingAs(user([
        'prompt_type.view-any',
        'prompt_type.*.update',
        'prompt_type.*.delete',
    ], LicenseType::ConversationalAi));

    $record = PromptType::factory()->create();

    assertDatabaseCount(PromptType::class, 1);

    assertDatabaseHas(PromptType::class, $record->toArray());

    livewire(EditPromptType::class, [
        'record' => $record->getRouteKey(),
    ])
        ->assertSuccessful()
        ->assertActionExists(DeleteAction::class)
        ->assertActionEnabled(DeleteAction::class)
        ->callAction(DeleteAction::class);

    assertDatabaseCount(PromptType::class, 0);

    assertModelMissing($record);
});
