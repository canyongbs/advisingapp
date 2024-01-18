<?php

use function Pest\Laravel\get;
use function Pest\Laravel\actingAs;
use function Pest\Livewire\livewire;

use AdvisingApp\Assistant\Models\Prompt;

use function Pest\Laravel\assertDatabaseCount;

use AdvisingApp\Authorization\Enums\LicenseType;
use AdvisingApp\Assistant\Filament\Resources\PromptResource;
use AdvisingApp\Assistant\Filament\Resources\PromptResource\Pages\ListPrompts;

it('cannot render without a license', function () {
    actingAs(user(
        'prompt.view-any',
    ));

    get(PromptResource::getUrl())
        ->assertForbidden();
});

it('cannot render without permissions', function () {
    actingAs(user(
        licenses: LicenseType::ConversationalAi
    ));

    get(PromptResource::getUrl())
        ->assertForbidden();
});

it('can render', function () {
    actingAs(user(
        'prompt.view-any',
        LicenseType::ConversationalAi
    ));

    get(PromptResource::getUrl())
        ->assertSuccessful();
});

it('can list records', function () {
    actingAs(user(
        'prompt.view-any',
        LicenseType::ConversationalAi
    ));

    assertDatabaseCount(Prompt::class, 0);

    $records = Prompt::factory()->count(10)->create();

    assertDatabaseCount(Prompt::class, $records->count());

    livewire(ListPrompts::class)
        ->assertSuccessful()
        ->assertCountTableRecords($records->count())
        ->assertCanSeeTableRecords($records);
});
