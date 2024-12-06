<?php

/*
<COPYRIGHT>

    Copyright © 2016-2024, Canyon GBS LLC. All rights reserved.

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

use App\Models\User;
use Livewire\Livewire;

use function Tests\asSuperAdmin;

use AdvisingApp\Ai\Enums\AiModel;
use App\Settings\LicenseSettings;

use function Pest\Laravel\actingAs;

use AdvisingApp\Ai\Models\AiAssistant;
use AdvisingApp\Ai\Enums\AiApplication;
use AdvisingApp\Authorization\Enums\LicenseType;
use AdvisingApp\Ai\Filament\Pages\ManageAiSettings;
use AdvisingApp\Ai\Filament\Resources\AiAssistantResource;

it('renders successfully', function () {
    asSuperAdmin();

    Livewire::test(ManageAiSettings::class)
        ->assertStatus(200);
});

it('does not load if you do not have any permissions to access', function () {
    $user = User::factory()->licensed(LicenseType::ConversationalAi)->create();

    actingAs($user);

    Livewire::test(ManageAiSettings::class)
        ->assertStatus(403);
});

it('loads if you have the correct access to ai settings', function () {
    $user = User::factory()->licensed(LicenseType::ConversationalAi)->create();

    $user->givePermissionTo(['product_admin.view-any', 'product_admin.*.view']);

    actingAs($user);

    Livewire::test(ManageAiSettings::class)
        ->assertStatus(200);
});

it('cannot access the page if assistant is default', function () {
    $aiSettings = app(LicenseSettings::class);

    $aiSettings->data->addons->customAiAssistants = true;

    $aiSettings->save();

    $aiAssistant = AiAssistant::factory([
        'assistant_id' => fake()->uuid(),
        'application' => AiApplication::PersonalAssistant,
        'model' => AiModel::OpenAiGpt4o,
        'is_default' => true,
        'description' => fake()->paragraph(),
        'instructions' => 'No Instructions.',
    ])->create();

    asSuperAdmin()
        ->get(
            AiAssistantResource::getUrl('edit', [
                'record' => $aiAssistant,
            ]),
        )->assertForbidden();
});

it('can access the page if assistant is not default', function () {
    $aiSettings = app(LicenseSettings::class);

    $aiSettings->data->addons->customAiAssistants = true;

    $aiSettings->save();

    $aiAssistant = AiAssistant::factory([
        'assistant_id' => fake()->uuid(),
        'application' => AiApplication::PersonalAssistant,
        'model' => AiModel::OpenAiGpt4o,
        'is_default' => false,
        'description' => fake()->paragraph(),
        'instructions' => 'No Instructions.',
    ])->create();

    asSuperAdmin()
        ->get(
            AiAssistantResource::getUrl('edit', [
                'record' => $aiAssistant,
            ]),
        )->assertSuccessful();
});
