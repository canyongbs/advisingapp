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

it('cannot render without a license', function () {
    actingAs(user([
        'prompt.view-any',
        'prompt.create',
    ]));

    get(PromptResource::getUrl('create'))
        ->assertForbidden();
});

it('cannot render without permissions', function () {
    actingAs(user(licenses: LicenseType::ConversationalAi));

    get(PromptResource::getUrl('create'))
        ->assertForbidden();
});

it('can render', function () {
    actingAs(user([
        'prompt.view-any',
        'prompt.create',
    ], LicenseType::ConversationalAi));

    get(PromptResource::getUrl('create'))
        ->assertSuccessful();
});

it('can create a record', function () {
    actingAs(user([
        'prompt.view-any',
        'prompt.create',
    ], LicenseType::ConversationalAi));

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
