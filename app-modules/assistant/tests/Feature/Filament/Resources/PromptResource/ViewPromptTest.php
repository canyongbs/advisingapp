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

it('cannot render without a license', function () {
    actingAs(user([
        'prompt.view-any',
        'prompt.*.view',
    ]));

    $record = Prompt::factory()->create();

    get(PromptResource::getUrl('view', ['record' => $record]))
        ->assertForbidden();
});

it('cannot render without permissions', function () {
    actingAs(user(licenses: LicenseType::ConversationalAi));

    $record = Prompt::factory()->create();

    get(PromptResource::getUrl('view', ['record' => $record]))
        ->assertForbidden();
});

it('can render', function () {
    actingAs(user([
        'prompt.view-any',
        'prompt.*.view',
    ], LicenseType::ConversationalAi));

    $record = Prompt::factory()->create();

    get(PromptResource::getUrl('view', ['record' => $record]))
        ->assertSuccessful();
});

it('can view a record', function () {
    actingAs(user([
        'prompt.view-any',
        'prompt.*.view',
    ], LicenseType::ConversationalAi));

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
