<?php

/*
<COPYRIGHT>

    Copyright © 2016-2025, Canyon GBS LLC. All rights reserved.

    Advising App™ is licensed under the Elastic License 2.0. For more details,
    see https://github.com/canyongbs/advisingapp/blob/main/LICENSE.

    Notice:

    - You may not provide the software to third parties as a hosted or managed
      service, where the service provides users with access to any substantial set of
      the features or functionality of the software.
    - You may not move, change, disable, or circumvent the license key functionality
      in the software, and you may not remove or obscure any functionality in the
      software that is protected by the license key.
    - You may not alter, remove, or obscure any licensing, copyright, or other notices
      of the licensor in the software. Any use of the licensor’s trademarks is subject
      to applicable law.
    - Canyon GBS LLC respects the intellectual property rights of others and expects the
      same in return. Canyon GBS™ and Advising App™ are registered trademarks of
      Canyon GBS LLC, and we are committed to enforcing and protecting our trademarks
      vigorously.
    - The software solution, including services, infrastructure, and code, is offered as a
      Software as a Service (SaaS) by Canyon GBS LLC.
    - Use of this software implies agreement to the license terms and conditions as stated
      in the Elastic License 2.0.

    For more information or inquiries please visit our website at
    https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

use AdvisingApp\Ai\Filament\Resources\AiAssistantResource\Pages\ListAiAssistants;
use AdvisingApp\Ai\Models\AiAssistant;
use AdvisingApp\Authorization\Enums\LicenseType;
use App\Settings\LicenseSettings;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseCount;
use function Pest\Laravel\get;
use function Pest\Livewire\livewire;

/** @var array<LicenseType> $licenses */
$licenses = [
    LicenseType::ConversationalAi,
];

$permissions = [
    'assistant_custom.view-any',
];

it('cannot render without a license', function () use ($permissions) {
    actingAs(user(
        permissions: $permissions
    ));

    get(ListAiAssistants::getUrl())
        ->assertForbidden();
});

it('cannot render without permissions', function () use ($licenses) {
    actingAs(user(
        licenses: $licenses,
    ));

    get(ListAiAssistants::getUrl())
        ->assertForbidden();
});

it('cannot render without proper features enabled', function () use ($licenses, $permissions) {
    actingAs(user(
        licenses: $licenses,
        permissions: $permissions
    ));

    get(ListAiAssistants::getUrl())
        ->assertForbidden();
});

it('can render', function () use ($licenses, $permissions) {
    actingAs(user(
        licenses: $licenses,
        permissions: $permissions
    ));

    $settings = app(LicenseSettings::class);

    $settings->data->addons->customAiAssistants = true;

    $settings->save();

    get(ListAiAssistants::getUrl())
        ->assertSuccessful();
});

it('can list records', function () use ($licenses, $permissions) {
    actingAs(user(
        licenses: $licenses,
        permissions: $permissions
    ));

    $settings = app(LicenseSettings::class);

    $settings->data->addons->customAiAssistants = true;

    $settings->save();

    assertDatabaseCount(AiAssistant::class, 0);

    $records = AiAssistant::factory()->count(5)->create();

    assertDatabaseCount(AiAssistant::class, $records->count());

    livewire(ListAiAssistants::class)
        ->assertSuccessful()
        ->assertCountTableRecords($records->count())
        ->assertCanSeeTableRecords($records);
});
