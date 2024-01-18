<?php

use function Pest\Laravel\get;
use function Pest\Laravel\actingAs;
use function Pest\Livewire\livewire;

use AdvisingApp\Assistant\Models\PromptType;

use function Pest\Laravel\assertDatabaseCount;

use AdvisingApp\Authorization\Enums\LicenseType;
use AdvisingApp\Assistant\Filament\Resources\PromptTypeResource;
use AdvisingApp\Assistant\Filament\Resources\PromptTypeResource\Pages\ListPromptTypes;

it('cannot render without a license', function () {
    actingAs(user(
        'prompt_type.view-any',
    ));

    get(PromptTypeResource::getUrl())
        ->assertForbidden();
});

it('cannot render without permissions', function () {
    actingAs(user(
        licenses: LicenseType::ConversationalAi
    ));

    get(PromptTypeResource::getUrl())
        ->assertForbidden();
});

it('can render', function () {
    actingAs(user(
        'prompt_type.view-any',
        LicenseType::ConversationalAi
    ));

    get(PromptTypeResource::getUrl())
        ->assertSuccessful();
});

it('can list records', function () {
    actingAs(user(
        'prompt_type.view-any',
        LicenseType::ConversationalAi
    ));

    assertDatabaseCount(PromptType::class, 0);

    $records = PromptType::factory()->count(10)->create();

    assertDatabaseCount(PromptType::class, $records->count());

    livewire(ListPromptTypes::class)
        ->assertSuccessful()
        ->assertCountTableRecords($records->count())
        ->assertCanSeeTableRecords($records);
});
